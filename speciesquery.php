
<?php

require("./birdwalker.php");

class SpeciesQuery
{
	// constrain this query to a particular location
	var $mLocationID;
	// constrain this query to a particular county
	var $mCounty;
	// constrain this query to a particular state
	var $mState;

	// constrain this query to a particular year
	var $mYear;
	// constrain this query to a particular month
	var $mMonth;

	function SpeciesQuery()
	{
		$this->setLocationID("");
		$this->setYear("");
		$this->setMonth("");
		$this->setCounty("");
		$this->setState("");
	}

	function setLocationID($inValue) { $this->mLocationID = $inValue; }
	function setYear($inValue) { $this->mYear = $inValue; }
	function setMonth($inValue) { $this->mMonth = $inValue; }
	function setCounty($inValue) { $this->mCounty = $inValue; }
	function setState($inValue) { $this->mState = $inValue; }

	function getFromClause()
	{
		$otherWhereClauses = "";

		if ($this->mLocationID != "") {
			$otherTables = $otherTables . ", location";
		} elseif ($this->mCounty != "") {
			$otherTables = $otherTables . ", location";
		} elseif ($this->mState != "") {
			$otherTables = $otherTables . ", location";
		}

		return "
            FROM sighting, species" . $otherTables . " ";
	}


	function getWhereClause()
	{
		$whereClause = "WHERE species.Abbreviation=sighting.SpeciesAbbreviation";

		if ($this->mLocationID != "") {
			$whereClause = $whereClause . " AND location.objectid=" . $this->mLocationID;
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mCounty != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mCounty . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mState != "") {
			$whereClause = $whereClause . " AND location.State='" . $this->mState . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		}
		
		if ($this->mMonth !="") {
			$whereClause = $whereClause . " AND Month(TripDate)=" . $this->mMonth;
		}
		if ($this->mYear !="") {
			$whereClause = $whereClause . " AND Year(TripDate)=" . $this->mYear;
		}

		return $whereClause;
	}

	function getQuery()
	{
		return performQuery("
          SELECT DISTINCT species.CommonName, species.objectid, species.ABACountable " . 
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function getPageTitle()
	{
		$pageTitle = "";

		if ($this->mLocationID != "") {
			$locationInfo = getLocationInfo($this->mLocationID); 
			$pageTitle = $locationInfo["Name"];
		} elseif ($this->mCounty != "") {
			$pageTitle = $this->mCounty . " County";
		} elseif ($this->mState != "") {
			$pageTitle = getStateNameForAbbreviation($this->mState);
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

	function rightThumbnail()
	{
		rightThumbnail("
          SELECT sighting.*, rand() AS shuffle " .
			$this->getFromClause() . "  " .
			$this->getWhereClause() . "
            AND sighting.Photo='1'
            ORDER BY shuffle LIMIT 1");
	}

	function formatSpeciesByYearTable()
	{
		$annualTotal = performQuery("
          SELECT COUNT(DISTINCT species.objectid) AS count, year(sighting.TripDate) AS year " .
            $this->getFromClause() . " " .
		    $this->getWhereClause() . "
			GROUP BY year");

		formatSpeciesByYearTable($this->getWhereClause(), "pants", $annualTotal);
	}

	function formatSpeciesByMonthTable()
	{
		$annualTotal = performQuery("
          SELECT COUNT(DISTINCT species.objectid) AS count, month(sighting.TripDate) AS month " .
            $this->getFromClause() . " " .
		    $this->getWhereClause() . "
			GROUP BY month");

		formatSpeciesByMonthTable($this->getWhereClause(), "pants", $annualTotal);
	}
}
?>
