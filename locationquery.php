
<?php

class LocationQuery
{
	// constrain this query to a particular county
	var $mCounty;
	// constrain this query to a particular state
	var $mStateID;

	// constrain this query to a trip
	var $mTripID;
	// constrain this query to a particular month
	var $mMonth;
	// constrain this query to a particular year
	var $mYear;

	// constrain this query to a particular location
	var $mSpeciesID;
	// constrain this query to a particular family
	var $mFamily;
	// constrain this query to a particular order
	var $mOrder;

	function LocationQuery()
	{
		$this->setCounty("");
		$this->setStateID("");

		$this->setTripID("");
		$this->setMonth("");
		$this->setYear("");

		$this->setSpeciesID("");
		$this->setFamily("");
		$this->setOrder("");
	}

	function setCounty($inValue) { $this->mCounty = $inValue; }
	function setStateID($inValue) { $this->mStateID = $inValue; }

	function setTripID($inValue) { $this->mTripID = $inValue; }
	function setMonth($inValue) { $this->mMonth = $inValue; }
	function setYear($inValue) { $this->mYear = $inValue; }

	function setSpeciesID($inValue) { $this->mSpeciesID = $inValue; }
	function setFamily($inValue) { $this->mFamily = $inValue; }
	function setOrder($inValue) { $this->mOrder = $inValue; }

	function getSelectClause()
	{
 		$selectClause = "SELECT DISTINCT location.objectid, location.Name, location.County, location.State";

		return $selectClause;
	}

	function getFromClause()
	{
		$otherTables = "";

		if ($this->mSpeciesID != "") {
			$otherTables = $otherTables . ", species";
		} elseif ($this->mFamily != "") {
			$otherTables = $otherTables . ", species";
		} elseif ($this->mOrder != "") {
			$otherTables = $otherTables . ", species";
		}

		return "
            FROM sighting, location" . $otherTables . " ";
	}


	function getWhereClause()
	{
		$whereClause = "WHERE sighting.LocationName=location.Name";

		if ($this->mTripID != "") {
			$tripInfo = getTripInfo($this->mTripID);
			$whereClause = $whereClause . " AND sighting.TripDate='" . $tripInfo["Date"] . "'";
		}

		if ($this->mCounty != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mCounty . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mStateID != "") {
			$stateInfo = getStateInfo($this->mStateID);
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		}

		if ($this->mSpeciesID != "") {
			$whereClause = $whereClause . " AND species.objectid='" . $this->mSpeciesID . "'";
			$whereClause = $whereClause . " AND sighting.SpeciesAbbreviation=species.Abbreviation"; 
		} elseif ($this->mFamily != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mFamily * pow(10, 7) . " AND
              species.objectid < " . ($this->mFamily + 1) * pow(10, 7);
			$whereClause = $whereClause . " AND sighting.SpeciesAbbreviation=species.Abbreviation"; 
		} elseif ($this->mOrder != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mOrder * pow(10, 9) . " AND
              species.objectid < " . ($this->mOrder + 1) * pow(10, 9);
			$whereClause = $whereClause . " AND sighting.SpeciesAbbreviation=species.Abbreviation"; 
		}
		
		if ($this->mMonth !="") {
			$whereClause = $whereClause . " AND Month(TripDate)=" . $this->mMonth;
		}
		if ($this->mYear !="") {
			$whereClause = $whereClause . " AND Year(TripDate)=" . $this->mYear;
		}

		return $whereClause;
	}

	function setFromRequest($get)
	{
		$this->setCounty(param($_GET, "county", ""));
		$this->setStateID(param($_GET, "stateid", ""));

		$this->setTripID(param($_GET, "tripid", ""));
		$this->setMonth(param($_GET, "month", ""));
		$this->setYear(param($_GET, "year", ""));

		$this->setSpeciesID(param($_GET, "speciesid", ""));
		$this->setFamily(param($_GET, "family", ""));
		$this->setOrder(param($_GET, "order", ""));
	}

	function getParams()
	{
		$params = "";

		if ($this->mCounty != "") {
			$params = $params . "&county=" . $this->mCounty;
		}
		if ($this->mStateID != "") {
			$params = $params . "&stateid=" . $this->mStateID;
		}

		if ($this->mTripID != "") {
			$params = $params . "&tripid=" . $this->mTripID;
		}

		if ($this->mSpeciesID != "") {
			$params = $params . "&speciesid=" . $this->mSpeciesID;
		} elseif ($this->mFamily != "") {
			$params = $params . "&family=" . $this->mFamily;
		} elseif ($this->mOrder != "") {
			$params = $params . "&order=" . $this->mOrder;
		}
		
		if ($this->mMonth !="") {
			$params = $params . "&month=" . $this->mMonth;
		} elseif ($this->mYear !="") {
			$params = $params . "&year=" . $this->mYear;
		}

		return $params;
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

	function getPageTitle()
	{
		// todo need to add species, family, order
		$pageTitle = "";

		if ($this->mSpeciesID != "") {
			$speciesInfo = getSpeciesInfo($this->mSpeciesID);
			$pageTitle = $speciesInfo["CommonName"];
		}

		if ($this->mCounty != "") {
			$pageTitle = $this->mCounty . " County";
		} elseif ($this->mStateID != "") {
			$stateInfo = getStateInfo($this->mStateID);
		    $pageTitle = $stateInfo["Name"];
		}
		if ($this->mMonth !="") {
			if ($pageTitle == "") $pageTitle = getMonthNameForNumber($this->mMonth);
			else $pageTitle = $pageTitle . ", " . getMonthNameForNumber($this->mMonth);
		}
		if ($this->mYear !="") {
			if ($pageTitle == "") $pageTitle = $this->mYear;
			else $pageTitle = $pageTitle . ", " . $this->mYear;
		}
		return $pageTitle; 
	}

	function findExtrema()
	{
		// TODO, we want both a minimum map dimension, and a minimum margin around the group of points
		$extrema = performOneRowQuery("
          SELECT
            max(location.Latitude) as maxLat, 
            min(location.Latitude) as minLat, 
            max(location.Longitude) as maxLong, 
            min(location.Longitude) as minLong " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " " .
			"AND location.Latitude<>'' AND location.Longitude<>''"); 

		$midLat = ($extrema["maxLat"] + $extrema["minLat"]) / 2.0;
		$midLong = ($extrema["maxLong"] + $extrema["minLong"]) / 2.0;
		$maxRadius = max(0.05, 1.1 * max($extrema["maxLong"] - $extrema["minLong"], $extrema["maxLat"] - $extrema["minLat"]) / 2.0);

		$box["minLat"] = $midLat - $maxRadius; $box["maxLat"] = $midLat + $maxRadius;
		$box["minLong"] = $midLong - $maxRadius; $box["maxLong"] = $midLong + $maxRadius;

		return $box;
	}

	function rightThumbnail()
	{
		rightThumbnail("
          SELECT sighting.*, rand() AS shuffle " .
			$this->getFromClause() . "  " .
			$this->getWhereClause() . "
            AND sighting.Photo='1'
            ORDER BY shuffle LIMIT 1");
	}

	function getPhotos()
	{
		return performQuery("
          SELECT sighting.* " .
			$this->getFromClause() . "  " .
			$this->getWhereClause() . "
            AND sighting.Photo='1'
            ORDER BY sighting.TripDate DESC");
	}

	function formatTwoColumnLocationList()
	{
		formatTwoColumnLocationList($this);
	}

	function formatLocationByYearTable()
	{
		$urlPrefix = "specieslist.php?";
		if ($this->mSpeciesID != "") $urlPrefix = "sightinglist.php?";

		formatLocationByYearTable($this, $urlPrefix, ($this->mCounty == ""));
	}

	function formatLocationByMonthTable()
	{
		$urlPrefix = "specieslist.php?";
		if ($this->mSpeciesID != "") $urlPrefix = "sightinglist.php?";

		formatLocationByMonthTable($this, $urlPrefix, ($this->mCounty == ""));
	}

	function formatPhotos()
	{
		formatPhotos($this);
	}
}
?>

