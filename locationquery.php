
<?php

require_once("./birdwalkerquery.php");

class LocationQuery extends BirdWalkerQuery
{
	function LocationQuery($inReq)
	{
		$this->BirdWalkerQuery($inReq);
	}

	function getSelectClause()
	{
 		$selectClause = "SELECT DISTINCT location.objectid, location.Name, location.County, location.State";

		return $selectClause;
	}

	function getFromClause()
	{
		$otherTables = "";

		if ($this->mReq->getSpeciesID() != "") {
			$otherTables = $otherTables . ", species";
		} elseif ($this->mReq->getFamilyID() != "") {
			$otherTables = $otherTables . ", species";
		} elseif ($this->mReq->getOrderID() != "") {
			$otherTables = $otherTables . ", species";
		}

		return "
            FROM sighting, location" . $otherTables . " ";
	}

	function getWhereClause()
	{
		$whereClause = "WHERE sighting.LocationName=location.Name";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = getTripInfo($this->mReq->getTripID());
			$whereClause = $whereClause . " AND sighting.TripDate='" . $tripInfo["Date"] . "'";
		}

		if ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mReq->getCounty() . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mReq->getLocationID() != "") {
			echo "<!-- LOCATION " . $this->mReq->getLocationID() . " -->\n";
			$whereClause = $whereClause . " AND location.objectid='" . $this->mReq->getLocationID() . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		}

		if ($this->mReq->getSpeciesID() != "") {
			$whereClause = $whereClause . " AND species.objectid='" . $this->mReq->getSpeciesID() . "'";
			$whereClause = $whereClause . " AND sighting.SpeciesAbbreviation=species.Abbreviation"; 
		} elseif ($this->mReq->getFamilyID() != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mReq->getFamilyID() * pow(10, 7) . " AND
              species.objectid < " . ($this->mReq->getFamilyID() + 1) * pow(10, 7);
			$whereClause = $whereClause . " AND sighting.SpeciesAbbreviation=species.Abbreviation"; 
		} elseif ($this->mReq->getOrderID() != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mReq->getOrderID() * pow(10, 9) . " AND
              species.objectid < " . ($this->mReq->getOrderID() + 1) * pow(10, 9);
			$whereClause = $whereClause . " AND sighting.SpeciesAbbreviation=species.Abbreviation"; 
		}
		
		if ($this->mReq->getMonth() !="") {
			$whereClause = $whereClause . " AND Month(TripDate)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(TripDate)=" . $this->mReq->getYear();
		}

		return $whereClause;
	}

	function getLocationCount()
	{
		return performCount("
          SELECT COUNT(DISTINCT location.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause());
	}

	function performQuery()
	{
		return performQuery("
          SELECT DISTINCT location.objectid, location.* ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY location.State, location.County, location.Name");
	}

	function findExtrema()
	{
		echo "<!-- extrema -->";

		// TODO, we want both a minimum map dimension, and a minimum margin around the group of points
		$extrema = performOneRowQuery("
          SELECT
            max(location.Latitude) as maxLat, 
            min(location.Latitude) as minLat, 
            max(location.Longitude) as maxLong, 
            min(location.Longitude) as minLong " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " " .
			"AND location.Latitude>0 AND location.Longitude<0"); 

		return $extrema;
	}

	function rightThumbnail()
	{
		rightThumbnail("
          SELECT sighting.*, " . dailyRandomSeedColumn() . " " .
			$this->getFromClause() . "  " .
			$this->getWhereClause() . "
            AND sighting.Photo='1'
            ORDER BY shuffle LIMIT 1");
	}

	function formatTwoColumnLocationList()
	{
		formatTwoColumnLocationList($this);
	}

	function formatLocationByYearTable()
	{
		formatLocationByYearTable($this);
	}

	function formatLocationByMonthTable()
	{
		formatLocationByMonthTable($this);
	}

	function formatPhotos()
	{
		formatPhotos($this);
	}
}
?>

