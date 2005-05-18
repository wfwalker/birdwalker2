<?php

require_once("./birdwalkerquery.php");

class SpeciesQuery extends BirdWalkerQuery
{
	function SpeciesQuery()
	{
		$this->setLocationID("");
		$this->setCounty("");
		$this->setStateID("");

		$this->setYear("");
		$this->setMonth("");

		$this->setFamily("");
		$this->setOrder("");
	}

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

		if ($this->mTripID == "")
		{
			$selectClause = $selectClause . ", min(sighting.Exclude) AS AllExclude";
		}

		// TODO how can we get a sightingid into this select clause even if it's not a specific trip id?
		if ($this->mTripID != "")
		{
			$selectClause = $selectClause . ", sighting.Notes, sighting.Exclude, sighting.Photo, sighting.objectid AS sightingid";
		}
		else if (($this->mLocationID != "") || ($this->mCounty != "") || ($this->mStateID != ""))
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
		} elseif ($this->mStateID != "") {
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
		} elseif ($this->mStateID != "") {
			$stateInfo = getStateInfo($this->mStateID);
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
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

	function getSpeciesCount()
	{
		return performCount("
          SELECT COUNT(DISTINCT species.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function getPhotoCount()
	{
		return performCount("
          SELECT COUNT(DISTINCT species.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1' ORDER BY species.objectid");
	}

	function performQuery()
	{
		return performQuery("
          SELECT DISTINCT species.objectid, species.* ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function rightThumbnail()
	{
		rightThumbnail("
          SELECT sighting.*, " . dailyRandomSeedColumn() . " " .
			$this->getFromClause() . "  " .
			$this->getWhereClause() . "
            AND sighting.Photo='1'
            ORDER BY shuffle LIMIT 1", true);
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
