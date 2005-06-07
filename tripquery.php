
<?php

require_once("./birdwalkerquery.php");

class TripQuery extends BirdWalkerQuery
{
	function TripQuery($inReq)
	{
		$this->BirdWalkerQuery($inReq);
	}

	function getSelectClause()
	{
 		$selectClause = "SELECT DISTINCT trip.objectid, trip.Name, trip.Notes, trip.Date, date_format(trip.Date, '%M %e') AS niceDate, year(trip.Date) as year";

		if ($this->mReq->getSpeciesID() != "")
		{
			$selectClause = $selectClause . ", sighting.Notes as sightingNotes, sighting.Exclude, sighting.Photo, sighting.objectid AS sightingid";
		}

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

		if ($this->mReq->getLocationID() != "") {
			$otherTables = $otherTables . ", location";
		} elseif ($this->mReq->getCounty() != "") {
			$otherTables = $otherTables . ", location";
		} elseif ($this->mReq->getStateID() != "") {
			$otherTables = $otherTables . ", location";
		}

		return "
            FROM sighting, trip" . $otherTables . " ";
	}


	function getWhereClause()
	{
		$whereClause = "WHERE sighting.TripDate=trip.Date";

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND location.objectid='" . $this->mReq->getLocationID() . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mReq->getCounty() . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = $this->mReq->getStateInfo();
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
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
			$whereClause = $whereClause . " AND Month(Date)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(Date)=" . $this->mReq->getYear();
		}

		return $whereClause;
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
 
	function formatTwoColumnTripList()
	{
		formatTwoColumnTripList($this);
	}

	function formatPhotos()
	{
		formatPhotos($this);
	}



	function formatSummaries()
	{
	  $dbQuery = $this->performQuery();

      while ($info = mysql_fetch_array($dbQuery))
	  {
          $tripSpeciesCount = performCount("
              SELECT COUNT(DISTINCT(sighting.objectid))
                  FROM sighting
                  WHERE sighting.TripDate='" . $info["Date"] . "'"); ?>

          <div class="pagesubtitle"><?= $info["niceDate"] ?></div>

		  <div class="titleblock">
              <span class="heading">
                  <a href="./tripdetail.php?tripid=<?=$info["objectid"]?>">
<?                    rightThumbnail("SELECT * FROM sighting WHERE Photo='1' AND TripDate='" . $info["Date"] . "' LIMIT 1", false); ?>
                      <?= $info["Name"] ?>
                  </a>
              </span>
              <div class="subheading"><?= $tripSpeciesCount ?> species</div>
          </div>


          <div class=report-content>
<?		    if (array_key_exists("Notes", $info)) { echo $info["Notes"]; } ?>
            <br clear="all"/>
          </div>
		  <p>&nbsp;</p>

<?	  }
	}
}
?>
