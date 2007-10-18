<?php

require_once("birdwalkerquery.php");

class LocationQuery extends BirdWalkerQuery
{
	function LocationQuery($inReq)
	{
		$this->BirdWalkerQuery($inReq);
	}

	function getSelectClause()
	{
 		$selectClause = "SELECT DISTINCT locations.id, locations.*, SUM(sightings.Photo) as locationPhotos, locations.Notes as locationNotes";

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

		return "
            FROM sightings, trips, locations" . $otherTables . " ";
	}

	function getWhereClause()
	{
		$whereClause = "WHERE sightings.location_id=locations.id AND sightings.trip_id=trips.id ";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = $this->mReq->getTripInfo();
			$whereClause = $whereClause . " AND sightings.trip_id='" . $tripInfo["id"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			echo "<!-- LOCATION " . $this->mReq->getLocationID() . " -->\n";
			$whereClause = $whereClause . " AND locations.id='" . $this->mReq->getLocationID() . "'";
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND locations.County='" . $this->mReq->getCounty() . "'";
			$whereClause = $whereClause . " AND locations.id=sightings.location_id"; 
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND locations.State='" . $stateInfo["Abbreviation"] . "'";
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
			$whereClause = $whereClause . " AND DayOfMonth(trips.Date)=" . $this->mReq->getDayOfMonth();
		}
		if ($this->mReq->getMonth() !="") {
			$whereClause = $whereClause . " AND Month(trips.Date)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(trips.Date)=" . $this->mReq->getYear();
		}

		return $whereClause;
	}

	function getLocationCount()
	{
		return performCount(
		  "Count Locations",
          "SELECT COUNT(DISTINCT locations.id) ".
			$this->getFromClause() . " " .
			$this->getWhereClause());
	}

	function getPhotoCount()
	{
		return performCount(
		  "Count Photos",
          "SELECT COUNT(DISTINCT locations.id) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sightings.Photo='1'");
	}

	function performQuery()
	{
		return performQuery("Perform Query",
			$this->getSelectClause() . " " . 
			$this->getFromClause() . " " .
			$this->getWhereClause() . " GROUP BY locations.id ORDER BY locations.State, locations.County, locations.Name");
	}

	function findExtrema()
	{
		// TODO, we want both a minimum map dimension, and a minimum margin around the group of points
		$extrema = performOneRowQuery("Find Location Extrema",
          "SELECT
            max(locations.Latitude) as maxLat, 
            min(locations.Latitude) as minLat, 
            max(locations.Longitude) as maxLong, 
            min(locations.Longitude) as minLong " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " " .
			"AND locations.Latitude>0 AND locations.Longitude<0"); 

		return $extrema;
	}

	function formatPhotos()
	{
		formatPhotos($this);
	}


	/**
	 * Show locations as rows, years as columns
	 */
	function formatLocationByYearTable()
	{
		$countyHeadingsOK = ($this->mReq->getCounty() == "");

		$lastStateHeading="";

		$gridQueryString="
        SELECT distinct(locations.Name), County, State, locations.id as locationid, bit_or(1 << (year(trips.Date) - 1995)) as mask " . 
		  $this->getFromClause() . " " .
		  $this->getWhereClause() . " 
        GROUP BY sightings.location_id
        ORDER BY locations.State, locations.County, locations.Name;";

		$gridQuery = performQuery("Location By Year Query", $gridQueryString); ?>

		<div class="onecolumn">
		  <table cellpadding="0" cellspacing="0" class="report-content" width="100%">
		    <tr><td></td><? insertYearLabels() ?></tr>

	<?  $prevInfo = "";
		while ($info = mysql_fetch_array($gridQuery))
		{
			$theMask = $info["mask"];

			if ($countyHeadingsOK && ($prevInfo == "" || $prevInfo["county"] != $info["county"]))
			{
				$stateInfo = getStateInfoForAbbreviation($info["state"]) ?>

				<tr>
				  <td colspan="13">
                    <div class="subheading">
	<?                if ($lastStateHeading != $info["state"]) { ?>
				        <span class="statename"><a href="./statedetail.php?stateid=<?= $stateInfo["id"] ?>"><?= $stateInfo["name"] ?></a></span>,
	<?                  $lastStateHeading = $info["state"];
				      } ?>
				      <a href="./countydetail.php?stateid=<?= $stateInfo["id"] ?>&county=<?= urlencode($info["county"]) ?>"><?= $info["county"] ?> County</a>
                    </div>
                  </td>
				</tr>
	<?		} ?>

			<tr>
				<td width="40%">
					<a href="./locationdetail.php?locationid=<?= $info["locationid"] ?>"><?= $info["name"] ?></a>
				</td>

	<?		for ($index = 1; $index <= (1 + getLatestYear() - getEarliestYear()); $index++)
			{ ?>
				<td class="bordered" align="center">
	<?			if (($theMask >> $index) & 1)
				{
					$clickRequest = new Request; // make a new request from current params and modify
					$clickRequest->setLocationID($info["locationid"]);
					$clickRequest->setYear(1995 + $index);
					$clickRequest->setView("");
					$clickRequest->setPageScript($this->mReq->getTimeAndLocationScript());
					echo $clickRequest->linkToSelf("X");
				}
				else
				{ ?>
					&nbsp;
	<?			} ?>
				</td>
	<?		} ?>
			</tr>
	<?
			$prevInfo = $info;
		} ?>

		 </table>
	   </div>
	<?
	} 


	/**
	 * Show locations as rows, months as columns
	 */
	function formatLocationByMonthTable()
	{
		$countyHeadingsOK = ($this->mReq->getCounty() == "");

		$lastStateHeading="";

		$gridQueryString="
        SELECT distinct(locations.Name), County, State, locations.id AS locationid, bit_or(1 << month(trips.Date)) AS mask " .
		  $this->getFromClause() . " " .
		  $this->getWhereClause() . " 
        GROUP BY sightings.location_id
        ORDER BY locations.State, locations.County, locations.Name;";

		$gridQuery = performQuery("Location By Month Query", $gridQueryString); ?>

		<div class="onecolumn">
		  <table cellpadding="0" cellspacing="0" cols="11" class="report-content" width="100%">
		  <tr><td></td><? insertMonthLabels() ?></tr>

	<?
		$prevInfo = "";
		while ($info = mysql_fetch_array($gridQuery))
		{
			$theMask = $info["mask"];

			if ($prevInfo == "" || $countyHeadingsOK && ($prevInfo["county"] != $info["county"])) {
				$stateInfo = getStateInfoForAbbreviation($info["state"]) ?>

				<tr><td colspan="13">
				  <div class="subheading">
	<?          if ($lastStateHeading != $info["state"]) { ?>
				  <span class="statename"><a href="./statedetail.php?stateid=<?= $stateInfo["id"] ?>"><?= $stateInfo["name"] ?></a></span>,
	<?            $lastStateHeading = $info["state"];
				} ?>
				  <a href="./countydetail.php?stateid=<?= $stateInfo["id"] ?>&county=<?= urlencode($info["county"]) ?>"><?= $info["county"] ?> County</a>
				  </div>
				  </td>
			    </tr>
	<?		} ?>

			<tr>
				<td>
					<a href="./locationdetail.php?locationid=<?= $info["locationid"] ?>"><?= $info["name"] ?></a>
				</td>

	<?		for ($index = 1; $index <= 12; $index++)
			{ ?>
				<td class="bordered" align="center">
	<?			if (($theMask >> $index) & 1)
				{
					$clickRequest = new Request; // make a new request from current params and modify
					$clickRequest->setLocationID($info["locationid"]);
					$clickRequest->setMonth($index);
					$clickRequest->setView("");
					$clickRequest->setPageScript($this->mReq->getTimeAndLocationScript());
					echo $clickRequest->linkToSelf("X");
				}
				else
				{ ?>
					&nbsp;
	<?			} ?>
				</td>
	<?		} ?>
			</tr>
	<?
			$prevInfo = $info;
		} ?>

		 </table>
	<?
	} 


	function formatTwoColumnLocationList()
	{
		$countyHeadingsOK = ($this->mReq->getCounty() == "");

		$dbQuery = $this->performQuery("Two Column Location List");

		$lastStateHeading="";
		$prevInfo=null;
		$locationCount = mysql_num_rows($dbQuery);
		$divideByCounties = ($locationCount > 20);
		$counter = round($locationCount  * 0.5);

		doubleCountHeading($locationCount, "location", $this->getPhotoCount(), "with photo"); ?>

		<table class="report-content" width="100%">
		  <tr valign="top"><td width="50%" class="leftcolumn">

	<?	while($info = mysql_fetch_array($dbQuery))
		{
			if ($countyHeadingsOK && $divideByCounties && (($prevInfo["state"] != $info["state"]) || ($prevInfo["county"] != $info["county"])))
			{ ?>
				<div class="subheading">
<?              if ($lastStateHeading != $info["state"])
				{
					$stateInfo = getStateInfoForAbbreviation($info["state"]); ?>
					<span class="statename"><a href="./statedetail.php?view=<?= $this->mReq->getView() ?>&stateid=<?= $stateInfo["id"]?>"><?= $stateInfo["name"] ?></a></span>,
<?                  $lastStateHeading = $info["state"];
				} ?>
				<a href="./countydetail.php?view=<?= $this->mReq->getView() ?>&stateid=<?= $stateInfo["id"]?>&county=<?= $info["county"] ?>">
			      <?= $info["county"] ?> County
			    </a>
				</div>
<?          } // TODO, list below the county and state name if not dividing by county/state ?>

			<div>
			  <a href="./locationdetail.php?view=species&locationid=<?= $info["id"] ?>"><?= $info["name"] ?></a>

<?          if ($info["locationPhotos"] > 0) { ?>
              <a href="./locationdetail.php?view=photo&locationid=<?= $info["id"] ?>">
			      <img border="0" align="bottom" src="./images/camera.gif" alt="photo">
			  </a>
<?           } ?>

<?          if (($this->mReq->getTripID() == "") && strlen($info["locationNotes"]) > 0) { ?>
              <div class="sighting-notes"><?= $info["locationNotes"] ?></div>
<?          } ?>

			</div>

	<?		$prevInfo = $info;   
			$counter--;
			if ($counter == 0)
			{ ?>
			</td><td width="50%" class="rightcolumn">
	<?		}
		} ?>

		</tr></table>
	<?
	}
}
?>
