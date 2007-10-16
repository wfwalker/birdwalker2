<?php

require_once("birdwalkerquery.php");


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
		return "SELECT DISTINCT sighting.id as sightingid,
            sighting.*,
            trip.id as tripid, " . shortNiceDateColumn("sighting.trip_id") . ", trip.*,
            location.id as locationid, location.*,
            species.id as speciesid, species.*";
	}

	function getFromClause()
	{
		return " FROM sighting, species, location, trip";
	}

	function getWhereClause()
	{
		$whereClause = "WHERE
            location.id=sighting.location_id AND
            species.id=sighting.species_id AND
            trip.id=sighting.trip_id ";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = $this->mReq->getTripInfo();
			$whereClause = $whereClause . " AND sighting.trip_id='" . $tripInfo["Date"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND location.id=" . $this->mReq->getLocationID();
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mReq->getCounty() . "'";
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
		}

		if ($this->mReq->getSpeciesID() != "") {
			$speciesInfo = getSpeciesInfo($this->mReq->getSpeciesID());
			$whereClause = $whereClause . " AND
              sighting.species_id='" . $speciesInfo["id"] . "'"; 
		} elseif ($this->mReq->getFamilyID() != "") {
			$whereClause = $whereClause . " AND
              species.id >= " . $this->mReq->getFamilyID() * pow(10, 7) . " AND
              species.id < " . ($this->mReq->getFamilyID() + 1) * pow(10, 7);
		} elseif ($this->mReq->getOrderID() != "") {
			$whereClause = $whereClause . " AND
              species.id >= " . $this->mReq->getOrderID() * pow(10, 9) . " AND
              species.id < " . ($this->mReq->getOrderID() + 1) * pow(10, 9);
		}
		
		if ($this->mReq->getMonth() !="") {
			$whereClause = $whereClause . " AND Month(trip.Date)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(trip.Date)=" . $this->mReq->getYear();
		}

		return $whereClause;
	}

	function getOrderByClause($inPrefix = "")
	{
	    if ($this->isSpeciesSpecified())
		{
		    return "ORDER BY sighting.trip_id DESC";
		}
		elseif ($this->isFamilySpecified() || $this->isOrderSpecified())
		{
		    return "ORDER BY species.id";
		}
		elseif ($this->isLocationSpecified() || $this->isCountySpecified() || $this->isStateSpecified())
		{
		    return "ORDER BY species.id";
		}
		elseif ($this->mReq->getMonth() != "" || $this->mReq->getYear() != "")
		{
		    return "ORDER BY species.id";
		}
		else
		{
			return "ORDER BY sighting.trip_id DESC";
		}
	}

	function performQuery()
	{
		if (($this->mReq->getLocationID() == "") && ($this->mReq->getCounty() == "") && ($this->mReq->getStateID() == "") &&
			($this->mReq->getTripID() == "") && ($this->mReq->getMonth() == "") && ($this->mReq->getYear() == "") &&
			($this->mReq->getFamilyID() == "") && ($this->mReq->getOrderID() == "") && ($this->mReq->getSpeciesID() == ""))
			die("No query parameters for sighting query");

		return performQuery("Find sightings", 
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY sighting.trip_id desc");
	}

	function performPhotoQuery()
	{
		return performQuery("Find sightings with photos", 
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1' " . 
			$this->getOrderByClause());
	}

	function formatPhotos()
	{
	    $numberOfColumns = 4;

		// TODO, the labels here should show values not fixed by the query!
		$dbQuery = $this->performPhotoQuery();

		countHeading(mysql_num_rows($dbQuery), "photo");

		$counter = $numberOfColumns; ?>

		<table class="report-content" width="100%">
		  <tr>

	<?
		while ($sightingInfo = mysql_fetch_array($dbQuery))
		{
		    $photoFilename = getPhotoFilename($sightingInfo); ?>

			<td height="130" width="25%" style="text-align: center">
			  <div><?= getThumbForSightingInfo($sightingInfo) ?></div>
			  <?= $this->getSightingTitle($sightingInfo) ?><br/>
			</td>

<?			$counter--;

			if ($counter == 0)
			{
              $counter = $numberOfColumns; ?>
			  </tr><tr>
	<?		}
		}

	?>
		   </td>
		</tr>
	  </table><?
	}

}
?>
