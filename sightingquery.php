<?php

require_once("./birdwalkerquery.php");


class SightingQuery extends BirdWalkerQuery
{
	function SightingQuery($inReq)
	{
		$this->BirdWalkerQuery($inReq);
	}

	function getSightingTitle($sightingInfo)
	{
		if (! $this->isSpeciesSpecified()) return $sightingInfo["CommonName"];
		if (! $this->isTripSpecified()) return $sightingInfo["niceDate"];
	}

	function getSightingSubtitle($sightingInfo)
	{
		if (! $this->isLocationSpecified()) return $sightingInfo["LocationName"] . ", " . $sightingInfo["State"];
		else return $sightingInfo["niceDate"];
	}

	function getSelectClause()
	{
		return "SELECT DISTINCT sighting.objectid as sightingid,
            sighting.*,
            trip.objectid as tripid, date_format(sighting.TripDate, '%M %e, %Y') AS niceDate, trip.*,
            location.objectid as locationid, location.*,
            species.objectid as speciesid, species.*";
	}

	function getFromClause()
	{
		return " FROM sighting, species, location, trip";
	}

	function getWhereClause()
	{
		$whereClause = "WHERE
            location.Name=sighting.LocationName AND
            species.Abbreviation=sighting.SpeciesAbbreviation AND
            trip.Date=sighting.TripDate ";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = getTripInfo($this->mReq->getTripID());
			$whereClause = $whereClause . " AND sighting.TripDate='" . $tripInfo["Date"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND location.objectid=" . $this->mReq->getLocationID();
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mReq->getCounty() . "'";
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
		}

		if ($this->mReq->getSpeciesID() != "") {
			$speciesInfo = getSpeciesInfo($this->mReq->getSpeciesID());
			$whereClause = $whereClause . " AND
              sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'"; 
		} elseif ($this->mReq->getFamilyID() != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mReq->getFamilyID() * pow(10, 7) . " AND
              species.objectid < " . ($this->mReq->getFamilyID() + 1) * pow(10, 7);
		} elseif ($this->mReq->getOrderID() != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mReq->getOrderID() * pow(10, 9) . " AND
              species.objectid < " . ($this->mReq->getOrderID() + 1) * pow(10, 9);
		}
		
		if ($this->mReq->getMonth() !="") {
			$whereClause = $whereClause . " AND Month(TripDate)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(TripDate)=" . $this->mReq->getYear();
		}

		return $whereClause;
	}

	function performQuery()
	{
		if (($this->mReq->getLocationID() == "") && ($this->mReq->getCounty() == "") && ($this->mReq->getStateID() == "") &&
			($this->mReq->getTripID() == "") && ($this->mReq->getMonth() == "") && ($this->mReq->getYear() == "") &&
			($this->mReq->getFamilyID() == "") && ($this->mReq->getOrderID() == "") && ($this->mReq->getSpeciesID() == ""))
			die("No query parameters for sighting query");

		return performQuery(
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY sighting.TripDate desc");
	}

	function performPhotoQuery()
	{
		if (($this->mReq->getLocationID() == "") && ($this->mReq->getCounty() == "") && ($this->mReq->getStateID() == "") &&
			($this->mReq->getTripID() == "") && ($this->mReq->getMonth() == "") && ($this->mReq->getYear() == "") &&
			($this->mReq->getFamilyID() == "") && ($this->mReq->getOrderID() == "") && ($this->mReq->getSpeciesID() == ""))
			die("No query parameters for sighting query");

		return performQuery(
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1' ORDER BY sighting.TripDate desc");
	}

	function rightThumbnail($anchorFlag)
	{
		rightThumbnail("
          SELECT sighting.*, " . dailyRandomSeedColumn() . " " .
			$this->getFromClause() . "  " .
			$this->getWhereClause() . "
            AND sighting.Photo='1'
            ORDER BY shuffle LIMIT 1", $anchorFlag);
	}

	function formatPhotos()
	{
		// TODO, the labels here should show values not fixed by the query!
		$dbQuery = $this->performPhotoQuery();

		countHeading(mysql_num_rows($dbQuery), "photo");

		$counter = round(mysql_num_rows($dbQuery)  * 0.5); ?>

		<table class="report-content" width="100%">
		  <tr><td width="50%" valign="top">

	<?
		while ($sightingInfo = mysql_fetch_array($dbQuery))
		{
			$tripInfo = getTripInfoForDate($sightingInfo["TripDate"]);
			$tripYear =  substr($tripInfo["Date"], 0, 4);
			$locationInfo = getLocationInfoForName($sightingInfo["LocationName"]);
	?>
				<div class=heading>
				  <div class=pagesubtitle>
					<?= $this->getSightingTitle($sightingInfo) ?>
	<?              editLink("./sightingedit.php?sightingid=" . $sightingInfo["sightingid"]); ?>
				  </div>
				  <div class=metadata>
					<?= $this->getSightingSubtitle($sightingInfo) ?>
				  </div>

	<?	    if ($sightingInfo["Photo"] == "1")
			{
				$photoFilename = getPhotoFilename($sightingInfo);

				list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename);

				echo getThumbForSightingInfo($sightingInfo);
			}

    ?>         </div> <?

			$counter--;

			if ($counter == 0)
			{ ?>
			</td><td valign="top" width="50%">
	<?		}
		}

	?>
		   </td>
		</tr>
	  </table><?
	}

}
?>
