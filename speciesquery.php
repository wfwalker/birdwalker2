
<?php

class SpeciesQuery
{
	// constrain this query to a particular location
	var $mLocationID;
	// constrain this query to a particular county
	var $mCounty;
	// constrain this query to a particular state
	var $mState;

	// constrain this query to a trip
	var $mTripID;
	// constrain this query to a particular month
	var $mMonth;
	// constrain this query to a particular year
	var $mYear;

	// constrain this query to a particular family
	var $mFamily;
	// constrain this query to a particular order
	var $mOrder;

	function SpeciesQuery()
	{
		$this->setLocationID("");
		$this->setCounty("");
		$this->setState("");

		$this->setYear("");
		$this->setMonth("");

		$this->setFamily("");
		$this->setOrder("");
	}

	function setLocationID($inValue) { $this->mLocationID = $inValue; }
	function setCounty($inValue) { $this->mCounty = $inValue; }
	function setState($inValue) { $this->mState = $inValue; }

	function setTripID($inValue) { $this->mTripID = $inValue; }
	function setMonth($inValue) { $this->mMonth = $inValue; }
	function setYear($inValue) { $this->mYear = $inValue; }

	function setFamily($inValue) { $this->mFamily = $inValue; }
	function setOrder($inValue) { $this->mOrder = $inValue; }

	function getGroupByClause()
	{
		if ($this->mTripID != "")
		{
			return "";
		}
		else
		{
			return "GROUP BY species.objectid";
		} 
	}

	function getSelectClause()
	{
		$selectClause = "SELECT DISTINCT species.objectid, species.CommonName, species.LatinName, species.ABACountable";

		// TODO how can we get a sightingid into this select clause even if it's not a specific trip id?
		if ($this->mTripID != "")
		{
			$selectClause = $selectClause . ", sighting.Notes, sighting.Exclude, sighting.Photo, sighting.objectid AS sightingid";
		}
		else if (($this->mLocationID != "") || ($this->mCounty != "") || ($this->mState != ""))
		{
			$selectClause = $selectClause . ",  min(concat(sighting.TripDate, lpad(sighting.objectid, 6, '0'))) as earliestsighting";
		}


		return $selectClause;
	}

	function getFromClause()
	{
		$otherTables = "";

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

		if ($this->mTripID != "") {
			$tripInfo = getTripInfo($this->mTripID);
			$whereClause = $whereClause . " AND sighting.TripDate='" . $tripInfo["Date"] . "'";
		}

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

		if ($this->mFamily != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mFamily * pow(10, 7) . " AND
              species.objectid < " . ($this->mFamily + 1) * pow(10, 7);
		} elseif ($this->mOrder != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mOrder * pow(10, 9) . " AND
              species.objectid < " . ($this->mOrder + 1) * pow(10, 9);
		}
		
		if ($this->mMonth !="") {
			$whereClause = $whereClause . " AND Month(TripDate)=" . $this->mMonth;
		}
		if ($this->mYear !="") {
			$whereClause = $whereClause . " AND Year(TripDate)=" . $this->mYear;
		}

		return $whereClause;
	}

	function getParams()
	{
		$params = "";

		if ($this->mLocationID != "") {
			$params = $params . "&locationid=" . $this->mLocationID;
		} elseif ($this->mCounty != "") {
			$params = $params . "&county=" . $this->mCounty;
		} elseif ($this->mState != "") {
			$params = $params . "&state=" . $this->mState;
		}

		if ($this->mFamily != "") {
			$params = $params . "&family=" . $this->mFamily;
		} elseif ($this->mOrder != "") {
			$params = $params . "&order=" . $this->mOrder;
		}
		
		if ($this->mMonth !="") {
			$params = $params . "&month=" . $this->mMonth;
		}
		if ($this->mYear !="") {
			$params = $params . "&year=" . $this->mYear;
		}

		return $params;
	}

	function getSpeciesCount()
	{
		return performCount("
          SELECT COUNT(DISTINCT species.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function performQuery()
	{
		return performQuery("
          SELECT DISTINCT species.objectid, species.* ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function getPageTitle()
	{
		// todo add species, family, order
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

		// todo, need order and family in here


		return $pageTitle; 
	}

	function rightThumbnail()
	{
		rightThumbnail("
          SELECT sighting.*, rand() AS shuffle " .
			$this->getFromClause() . "  " .
			$this->getWhereClause() . "
            AND sighting.Photo='1'
            ORDER BY shuffle LIMIT 1", true);
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

	function formatTwoColumnSpeciesList()
	{
		formatTwoColumnSpeciesList($this);
	}

	function formatSpeciesByYearTable()
	{
		$annualTotal = performQuery("
          SELECT COUNT(DISTINCT species.objectid) AS count, year(sighting.TripDate) AS year " .
            $this->getFromClause() . " " .
		    $this->getWhereClause() . "
			GROUP BY year");

		formatSpeciesByYearTable($this, $this->getParams(), $annualTotal);
	}

	function formatSpeciesByMonthTable()
	{
		$monthlyTotal = performQuery("
          SELECT COUNT(DISTINCT species.objectid) AS count, month(sighting.TripDate) AS month " .
            $this->getFromClause() . " " .
		    $this->getWhereClause() . "
			GROUP BY month");

		formatSpeciesByMonthTable($this, $this->getParams(), $monthlyTotal);
	}

	function formatPhotos()
	{
		formatPhotos($this);
	}
}

?>
