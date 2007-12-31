<?php

require_once("birdwalkerquery.php");

class TripQuery extends BirdWalkerQuery
{
	function TripQuery($inReq)
	{
		$this->BirdWalkerQuery($inReq);
	}

	function getSelectClause()
	{
 		$selectClause = "SELECT DISTINCT trips.id, trips.name, trips.notes, trips.date, date_format(trips.date, '%M %D') AS niceDate, year(trips.date) as year, sum(sightings.photo) as tripPhotos";

		if ($this->mReq->getSpeciesID() != "")
		{
			$selectClause = $selectClause . ", sightings.Notes as sightingNotes, sightings.Exclude, sightings.photo, sightings.id AS sightingid";
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
			$otherTables = $otherTables . ", locations";
		} elseif ($this->mReq->getCountyID() != "") {
			$otherTables = $otherTables . ", locations";
		} elseif ($this->mReq->getStateID() != "") {
			$otherTables = $otherTables . ", locations";
		}

		return "
            FROM sightings, trips" . $otherTables . " ";
	}


	function getWhereClause()
	{
		$whereClause = "WHERE sightings.trip_id=trips.id";

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND locations.id='" . $this->mReq->getLocationID() . "'";
		} elseif ($this->mReq->getCountyID() != "") {
			$whereClause = $whereClause . " AND locations.county_id='" . $this->mReq->getCountyID() . "'";
			$whereClause = $whereClause . " AND locations.id=sightings.location_id"; 
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = $this->mReq->getStateInfo();
			$whereClause = $whereClause . " AND locations.State='" . $stateInfo["abbreviation"] . "'";
			$whereClause = $whereClause . " AND locations.id=sightings.location_id"; 
		}

		if ($this->mReq->getSpeciesID() != "") {
			$whereClause = $whereClause . " AND species.id='" . $this->mReq->getSpeciesID() . "'";
			$whereClause = $whereClause . " AND sightings.species_id=species.id"; 
		} elseif ($this->mReq->getFamilyID() != "") {
			$whereClause = $whereClause . " AND
              species.id >= " . $this->mReq->getFamilyID() * pow(10, 7) . " AND
              species.id < " . ($this->mReq->getFamilyID() + 1) * pow(10, 7);
			$whereClause = $whereClause . " AND sightings.species_id=species.id"; 
		} elseif ($this->mReq->getOrderID() != "") {
			$whereClause = $whereClause . " AND
              species.id >= " . $this->mReq->getOrderID() * pow(10, 9) . " AND
              species.id < " . ($this->mReq->getOrderID() + 1) * pow(10, 9);
			$whereClause = $whereClause . " AND sightings.species_id=species.id"; 
		}
		
		if ($this->mReq->getDayOfMonth() !="") {
			$whereClause = $whereClause . " AND DayOfMonth(date)=" . $this->mReq->getDayOfMonth();
		}
		if ($this->mReq->getMonth() !="") {
			$whereClause = $whereClause . " AND Month(date)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(date)=" . $this->mReq->getYear();
		}

		return $whereClause;
	}

	function getTripCount()
	{
		return performCount(
		  "Count Trips", 
          "SELECT COUNT(DISTINCT trips.id) ".
			$this->getFromClause() . " " .
			$this->getWhereClause());
	}

	function getPhotoCount()
	{
		return performCount(
		  "Count Photos",
          "SELECT COUNT(DISTINCT trips.id) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sightings.photo='1'");
	}

	function performQuery()
	{
		return performQuery(
			"Query Trips",
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . "  GROUP BY trips.id ORDER BY trips.Date desc");
	}

	function formatPhotos()
	{
		formatPhotos($this);
	}

	function formatTwoColumnTripList()
	{
	    $dbQuery = $this->performQuery();
	    $tripCount = mysql_num_rows($dbQuery);
		$subdivideByYears = ($tripCount > 20) && ($this->mReq->getYear() == "");
		$prevYear = "";
		$counter = round($tripCount  * 0.52);

		doubleCountHeading($tripCount, "trip", $this->getPhotoCount(), "with photo"); ?>

	   <table class="report-content" width="100%">
		  <tr valign="top"><td width="50%" class="leftcolumn">

	<?	$dbQuery = $this->performQuery();
		while($info = mysql_fetch_array($dbQuery))
		{
			$thisYear =  substr($info["date"], 0, 4);

			if (strcmp($thisYear, $prevYear) && $subdivideByYears)
			{ ?>
				<div class="subheading">
					<a name="<?= $thisYear ?>"></a>
					<a href="./yeardetail.php?year=<?= $info["year"] ?>"><?= $info["year"] ?></a>
				</div>
	<?		} ?>

				 <div>
					<a href="./tripdetail.php?tripid=<?= $info["id"] ?>">
					  <?= $info["name"] ?>, <?= $info["niceDate"] ?><? if (($this->mReq->getYear() == "") && (! $subdivideByYears)) { echo ", " . $info["year"]; } ?>
					</a>
					<? if (array_key_exists("photo", $info) && $info["photo"] == "1") { ?><?= getPhotoLinkForSightingInfo($info, "sightingid") ?><? }
			           else if (array_key_exists("tripPhotos", $info) && $info["tripPhotos"] > 0)
					   { ?>
						   <a href="./tripdetail.php?view=photo&tripid=<?= $info["id"] ?>">
							   <img border="0" src="./images/camera.gif"/>
						   </a>
<?					   } ?>
					<? if (array_key_exists("exclude", $info) && $info["exclude"] == "1") { ?>excluded<? } ?>
				 </div>
				    <? if (array_key_exists("sightingNotes", $info) && $info["sightingNotes"] != "") { ?> <div class="sighting-notes"><?= stripslashes($info["sightingNotes"]) ?></div> <? } ?>

	<?		$prevYear = $thisYear;
			$counter--;
			if ($counter == 0)
			{ ?>
			</td><td width="50%" class="rightcolumn">
	<?		}
		} ?>
		  </td></tr>
		</table> <?
	}

	function formatSummaries()
	{
	  TripQuery::formatSummariesForDBQuery($this->performQuery());
	}

	function formatSummariesForDBQuery($dbQuery)
	{

      while ($info = mysql_fetch_array($dbQuery))
	  {
          $tripSpeciesCount = performCount(
			  "Count Sightings", 
              "SELECT COUNT(DISTINCT(sightings.id))
                  FROM sightings, trips
                  WHERE sightings.trip_id=trips.id AND trips.Date='" . $info["date"] . "'"); ?>

          <div class="superheading"><?= $info["niceDate"] ?></div>

		  <div class="summaryblock">
              <span class="subheading">
                  <a href="./tripdetail.php?tripid=<?=$info["id"]?>">
<?                    rightThumbnail("SELECT * FROM sightings, trips WHERE sightings.trip_id=trips.id AND Photo='1' AND trips.Date='" . $info["date"] . "' LIMIT 1", false); ?>
                      <?= $info["name"] ?>
                  </a>
              </span>
              <div class="subheading"><?= $tripSpeciesCount ?> species</div>
          </div>


          <div class="report-content">
<?		    if (array_key_exists("notes", $info)) { echo stripslashes($info["notes"]); } ?>
            <br clear="all"/>
          </div>
		  <p>&nbsp;</p>

<?	  }
	}
}
?>
