
<?php

class TripQuery
{
	// constrain this query to a particular location
	var $mLocationID;
	// constrain this query to trips that visited a particular county
	var $mCounty;
	// constrain this query to trips that visited a particular state
	var $mState;

	// constrain this query to trips during a particular month
	var $mMonth;
	// constrain this query to trips during a particular year
	var $mYear;

	// constrain this query to trips when we observed a particular species
	var $mSpeciesID;
	// constrain this query to trips when we observed a particular family
	var $mFamily;
	// constrain this query to trips when we observed a particular order
	var $mOrder;

	function TripQuery()
	{
		$this->setLocationID("");
		$this->setCounty("");
		$this->setState("");

		$this->setMonth("");
		$this->setYear("");

		$this->setSpeciesID("");
		$this->setFamily("");
		$this->setOrder("");
	}

	function setLocationID($inValue) { $this->mLocationID = $inValue; }
	function setCounty($inValue) { $this->mCounty = $inValue; }
	function setState($inValue) { $this->mState = $inValue; }

	function setMonth($inValue) { $this->mMonth = $inValue; }
	function setYear($inValue) { $this->mYear = $inValue; }

	function setSpeciesID($inValue) { $this->mSpeciesID = $inValue; }
	function setFamily($inValue) { $this->mFamily = $inValue; }
	function setOrder($inValue) { $this->mOrder = $inValue; }

	function getSelectClause()
	{
 		$selectClause = "SELECT DISTINCT trip.objectid, trip.Name, trip.Date, date_format(trip.Date, '%M %e') AS niceDate, year(trip.Date) as year";

		if ($this->mSpeciesID != "")
		{
			$selectClause = $selectClause . ", sighting.Notes as sightingNotes, sighting.Exclude, sighting.Photo, sighting.objectid AS sightingid";
		}

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

		if ($this->mLocationID != "") {
			$otherTables = $otherTables . ", location";
		} elseif ($this->mCounty != "") {
			$otherTables = $otherTables . ", location";
		} elseif ($this->mState != "") {
			$otherTables = $otherTables . ", location";
		}

		return "
            FROM sighting, trip" . $otherTables . " ";
	}


	function getWhereClause()
	{
		$whereClause = "WHERE sighting.TripDate=trip.Date";

		if ($this->mLocationID != "") {
			$whereClause = $whereClause . " AND location.objectid='" . $this->mLocationID . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mCounty != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mCounty . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mState != "") {
			$whereClause = $whereClause . " AND location.State='" . $this->mState . "'";
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
			$whereClause = $whereClause . " AND Month(Date)=" . $this->mMonth;
		}
		if ($this->mYear !="") {
			$whereClause = $whereClause . " AND Year(Date)=" . $this->mYear;
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

	function getTripCount()
	{
		return performCount("
          SELECT COUNT(DISTINCT trip.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause());
	}

	function performQuery()
	{
		return performQuery(
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY trip.Date desc");
	}

	function getPageTitle()
	{
		// todo need to add species, family, order
		$pageTitle = "";

		if ($this->mLocationID != "") {
			$locationID = getLocationInfo($this->mLocationID);
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

	function formatTwoColumnTripList()
	{
		formatTwoColumnTripList($this);
	}

	function formatPhotos()
	{
		formatPhotos($this);
	}
}
?>
