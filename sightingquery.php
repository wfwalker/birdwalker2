<?php

require_once("./birdwalkerquery.php");


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

		if ($this->mReq->getTripID() != "") {
			$tripInfo = getTripInfo($this->mReq->getTripID());
			$whereClause = $whereClause . " AND sighting.TripDate='" . $tripInfo["Date"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND location.objectid=" . $this->mReq->getLocationID();
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mReq->getCounty() . "'";
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
		}

		if ($this->mReq->getSpeciesID() != "") {
			$speciesInfo = getSpeciesInfo($this->mReq->getSpeciesID());
			$whereClause = $whereClause . " AND
              sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'"; 
		} elseif ($this->mReq->getFamilyID() != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mReq->getFamilyID() * pow(10, 7) . " AND
              species.objectid < " . ($this->mReq->getFamilyID() + 1) * pow(10, 7);
		} elseif ($this->mReq->getOrderID() != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mReq->getOrderID() * pow(10, 9) . " AND
              species.objectid < " . ($this->mReq->getOrderID() + 1) * pow(10, 9);
		}
		
		if ($this->mReq->getMonth() !="") {
			$whereClause = $whereClause . " AND Month(TripDate)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(TripDate)=" . $this->mReq->getYear();
		}

		return $whereClause;
	}

	function performQuery()
	{
		if (($this->mReq->getLocationID() == "") && ($this->mReq->getCounty() == "") && ($this->mReq->getStateID() == "") &&
			($this->mReq->getTripID() == "") && ($this->mReq->getMonth() == "") && ($this->mReq->getYear() == "") &&
			($this->mReq->getFamilyID() == "") && ($this->mReq->getOrderID() == "") && ($this->mReq->getSpeciesID() == ""))
			die("No query parameters for sighting query");

		return performQuery(
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY sighting.TripDate desc");
	}

	function performPhotoQuery()
	{
		if (($this->mReq->getLocationID() == "") && ($this->mReq->getCounty() == "") && ($this->mReq->getStateID() == "") &&
			($this->mReq->getTripID() == "") && ($this->mReq->getMonth() == "") && ($this->mReq->getYear() == "") &&
			($this->mReq->getFamilyID() == "") && ($this->mReq->getOrderID() == "") && ($this->mReq->getSpeciesID() == ""))
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

	function formatPhotos()
	{
		formatPhotos($this);
	}
}
?>
