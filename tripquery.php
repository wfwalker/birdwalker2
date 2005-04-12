
<?php

require_once("./birdwalkerquery.php");

class TripQuery extends BirdWalkerQuery
{
	function TripQuery()
	{
		$this->setLocationID("");
		$this->setCounty("");
		$this->setStateID("");

		$this->setMonth("");
		$this->setYear("");

		$this->setSpeciesID("");
		$this->setFamily("");
		$this->setOrder("");
	}

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
		} elseif ($this->mStateID != "") {
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
		} elseif ($this->mStateID != "") {
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
		} elseif ($this->mStateID != "") {
			$params = $params . "&stateid=" . $this->mStateID;
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

	function rightThumbnail()
	{
		rightThumbnail("
          SELECT sighting.*, " . dailyRandomSeedColumn() . " " .
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
