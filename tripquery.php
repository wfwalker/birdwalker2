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
 		$selectClause = "SELECT DISTINCT trip.objectid, trip.Name, trip.Notes, trip.Date, date_format(trip.Date, '%M %D') AS niceDate, year(trip.Date) as year, sum(sighting.Photo) as tripPhotos";

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
		return performCount(
		  "Count Trips", 
          "SELECT COUNT(DISTINCT trip.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause());
	}

	function getPhotoCount()
	{
		return performCount(
		  "Count Photos",
          "SELECT COUNT(DISTINCT trip.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1'");
	}

	function performQuery()
	{
		return performQuery(
			"Query Trips",
			$this->getSelectClause() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . "  GROUP BY trip.objectid ORDER BY trip.Date desc");
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
			$thisYear =  substr($info["Date"], 0, 4);

			if (strcmp($thisYear, $prevYear) && $subdivideByYears)
			{ ?>
				<div class="subheading">
					<a name="<?= $thisYear ?>"></a>
					<a href="./yeardetail.php?year=<?= $info["year"] ?>"><?= $info["year"] ?></a>
				</div>
	<?		} ?>

				 <div>
					<a href="./tripdetail.php?tripid=<?= $info["objectid"] ?>">
					  <?= $info["Name"] ?>, <?= $info["niceDate"] ?><? if (($this->mReq->getYear() == "") && (! $subdivideByYears)) { echo ", " . $info["year"]; } ?>
					</a>
					<? if (array_key_exists("Photo", $info) && $info["Photo"] == "1") { ?><?= getPhotoLinkForSightingInfo($info, "sightingid") ?><? }
			           else if (array_key_exists("tripPhotos", $info) && $info["tripPhotos"] > 0)
					   { ?>
						   <a href="./tripdetail.php?view=photo&tripid=<?= $info["objectid"] ?>">
							   <img border="0" src="./images/camera.gif"/>
						   </a>
<?					   } ?>
					<? if (array_key_exists("Exclude", $info) && $info["Exclude"] == "1") { ?>excluded<? } ?>
				 </div>
					<? if (array_key_exists("sightingNotes", $info) && $info["sightingNotes"] != "") { ?> <div class="sighting-notes"><?= $info["sightingNotes"] ?></div> <? } ?>

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
	  $dbQuery = $this->performQuery();

      while ($info = mysql_fetch_array($dbQuery))
	  {
          $tripSpeciesCount = performCount(
			  "Count Sightings", 
              "SELECT COUNT(DISTINCT(sighting.objectid))
                  FROM sighting
                  WHERE sighting.TripDate='" . $info["Date"] . "'"); ?>

          <div class="superheading"><?= $info["niceDate"] ?></div>

		  <div class="summaryblock">
              <span class="subheading">
                  <a href="./tripdetail.php?tripid=<?=$info["objectid"]?>">
<?                    rightThumbnail("SELECT * FROM sighting WHERE Photo='1' AND TripDate='" . $info["Date"] . "' LIMIT 1", false); ?>
                      <?= $info["Name"] ?>
                  </a>
              </span>
              <div class="subheading"><?= $tripSpeciesCount ?> species</div>
          </div>


          <div class="report-content">
<?		    if (array_key_exists("Notes", $info)) { echo $info["Notes"]; } ?>
            <br clear="all"/>
          </div>
		  <p>&nbsp;</p>

<?	  }
	}
}
?>