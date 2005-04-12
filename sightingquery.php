<?php

require_once("./birdwalkerquery.php");


class SightingQuery extends BirdWalkerQuery
{
	function SightingQuery()
	{
		$this->setLocationID("");
		$this->setCounty("");
		$this->setStateID("");

		$this->setTripID("");
		$this->setMonth("");
		$this->setYear("");

		$this->setSpeciesID("");
		$this->setFamily("");
		$this->setOrder("");
	}

	function getSightingTitle($sightingInfo)
	{
		if ($this->mSpeciesID == "") return $sightingInfo["CommonName"];
		if ($this->mTripID == "") return $sightingInfo["niceDate"];
	}

	function getSightingSubtitle($sightingInfo)
	{
		if ($this->mLocationID == "") return $sightingInfo["LocationName"];
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

		if ($this->mTripID != "") {
			$tripInfo = getTripInfo($this->mTripID);
			$whereClause = $whereClause . " AND sighting.TripDate='" . $tripInfo["Date"] . "'";
		}

		if ($this->mLocationID != "") {
			$whereClause = $whereClause . " AND location.objectid=" . $this->mLocationID;
		} elseif ($this->mCounty != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mCounty . "'";
		} elseif ($this->mStateID != "") {
			$stateInfo = getStateInfo($this->mStateID);
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
		}

		if ($this->mSpeciesID != "") {
			$speciesInfo = getSpeciesInfo($this->mSpeciesID);
			$whereClause = $whereClause . " AND
              sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'"; 
		} elseif ($this->mFamily != "") {
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
			$params = $params . "&county=" . $this->mCounty . "'";
		} elseif ($this->mState != "") {
			$params = $params . "&stateid=" . $this->mStateID . "'";
		}

		if ($this->mSpeciesID != "") {
			$params = $params . "&speciesID=" . $this->mSpeciesID;
		} elseif ($this->mFamily != "") {
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

	function performQuery()
	{
		if (($this->mLocationID == "") && ($this->mCounty == "") && ($this->mState == "") &&
			($this->mTripID == "") && ($this->mMonth == "") && ($this->mYear == "") &&
			($this->mFamily == "") && ($this->mOrder == "") && ($this->mSpeciesID == ""))
			die("No query parameters for sighting query");

		return performQuery(
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY sighting.TripDate desc");
	}

	function performPhotoQuery()
	{
		if (($this->mLocationID == "") && ($this->mCounty == "") && ($this->mState == "") &&
			($this->mTripID == "") && ($this->mMonth == "") && ($this->mYear == "") &&
			($this->mFamily == "") && ($this->mOrder == "") && ($this->mSpeciesID == ""))
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

// 	function getPhotos()
// 	{
// 		return performQuery("
//           SELECT sighting.* " .
// 			$this->getFromClause() . "  " .
// 			$this->getWhereClause() . "
//             AND sighting.Photo='1'
//             ORDER BY sighting.TripDate DESC");
// 	}

// 	function getPhotoCount()
// 	{
// 		return performCount("
//           SELECT COUNT(DISTINCT sighting.objectid) " .
// 			$this->getFromClause() . "  " .
// 			$this->getWhereClause() . "
//             AND sighting.Photo='1'");
// 	}

	function formatPhotos()
	{
		formatPhotos($this);
	}
}
?>
