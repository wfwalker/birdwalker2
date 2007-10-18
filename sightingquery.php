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
		if (! $this->isSpeciesSpecified()) return $sightingInfo["common_name"];
		if (! $this->isTripSpecified()) return $sightingInfo["niceDate"];
	}

	function getSightingSubtitle($sightingInfo)
	{
		if (! $this->isLocationSpecified()) return $sightingInfo["LocationName"] . ", " . $sightingInfo["state"];
		else return $sightingInfo["niceDate"];
	}

	function getSelectClause()
	{
		return "SELECT DISTINCT sightings.id as sightingid,
            sightings.*,
            trips.id as tripid, " . shortNiceDateColumn("sightings.trip_id") . ", trips.*,
            locations.id as locationid, locations.*,
            species.id as speciesid, species.*";
	}

	function getFromClause()
	{
		return " FROM sightings, species, locations, trips";
	}

	function getWhereClause()
	{
		$whereClause = "WHERE
            locations.id=sightings.location_id AND
            species.id=sightings.species_id AND
            trips.id=sightings.trip_id ";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = $this->mReq->getTripInfo();
			$whereClause = $whereClause . " AND sightings.trip_id='" . $tripInfo["date"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND locations.id=" . $this->mReq->getLocationID();
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND locations.County='" . $this->mReq->getCounty() . "'";
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND locations.State='" . $stateInfo["Abbreviation"] . "'";
		}

		if ($this->mReq->getSpeciesID() != "") {
			$speciesInfo = getSpeciesInfo($this->mReq->getSpeciesID());
			$whereClause = $whereClause . " AND
              sightings.species_id='" . $speciesInfo["id"] . "'"; 
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
			$whereClause = $whereClause . " AND Month(trips.Date)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(trips.Date)=" . $this->mReq->getYear();
		}

		return $whereClause;
	}

	function getOrderByClause($inPrefix = "")
	{
	    if ($this->isSpeciesSpecified())
		{
		    return "ORDER BY sightings.trip_id DESC";
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
			return "ORDER BY sightings.trip_id DESC";
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
			$this->getWhereClause() . " ORDER BY sightings.trip_id desc");
	}

	function performPhotoQuery()
	{
		return performQuery("Find sightings with photos", 
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sightings.Photo='1' " . 
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
