
<?php

require_once("./birdwalkerquery.php");

class LocationQuery extends BirdWalkerQuery
{
	function LocationQuery($inReq)
	{
		$this->BirdWalkerQuery($inReq);
	}

	function getSelectClause()
	{
 		$selectClause = "SELECT DISTINCT location.objectid, location.*, SUM(sighting.Photo) as locationPhotos";

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
            FROM sighting, location" . $otherTables . " ";
	}

	function getWhereClause()
	{
		$whereClause = "WHERE sighting.LocationName=location.Name";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = getTripInfo($this->mReq->getTripID());
			$whereClause = $whereClause . " AND sighting.TripDate='" . $tripInfo["Date"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			echo "<!-- LOCATION " . $this->mReq->getLocationID() . " -->\n";
			$whereClause = $whereClause . " AND location.objectid='" . $this->mReq->getLocationID() . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mReq->getCounty() . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
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
			$whereClause = $whereClause . " AND Month(TripDate)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(TripDate)=" . $this->mReq->getYear();
		}

		return $whereClause;
	}

	function getLocationCount()
	{
		return performCount(
		  "Count Locations",
          "SELECT COUNT(DISTINCT location.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause());
	}

	function getPhotoCount()
	{
		return performCount(
		  "Count Photos",
          "SELECT COUNT(DISTINCT location.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1'");
	}

	function performQuery()
	{
		return performQuery("Perform Query",
			$this->getSelectClause() . " " . 
			$this->getFromClause() . " " .
			$this->getWhereClause() . " GROUP BY location.objectid ORDER BY location.State, location.County, location.Name");
	}

	function findExtrema()
	{
		// TODO, we want both a minimum map dimension, and a minimum margin around the group of points
		$extrema = performOneRowQuery("Find Location Extrema",
          "SELECT
            max(location.Latitude) as maxLat, 
            min(location.Latitude) as minLat, 
            max(location.Longitude) as maxLong, 
            min(location.Longitude) as minLong " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " " .
			"AND location.Latitude>0 AND location.Longitude<0"); 

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
        SELECT distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask " . 
		  $this->getFromClause() . " " .
		  $this->getWhereClause() . " 
        GROUP BY sighting.LocationName
        ORDER BY location.State, location.County, location.Name;";

		$gridQuery = performQuery("Location By Year Query", $gridQueryString); ?>

		<table cellpadding=0 cellspacing=0 class="report-content" width="100%">
		<tr><td></td><? insertYearLabels() ?></tr>

	<?  $prevInfo = "";
		while ($info = mysql_fetch_array($gridQuery))
		{
			$theMask = $info["mask"];

			if ($countyHeadingsOK && ($prevInfo == "" || $prevInfo["County"] != $info["County"]))
			{
				$stateInfo = getStateInfoForAbbreviation($info["State"]) ?>

				<tr><td class=subheading colspan=13>
	<?          if ($lastStateHeading != $info["State"]) { ?>
				  <span class="statename"><a href="./statedetail.php?stateid=<?= $stateInfo["objectid"] ?>"><?= $stateInfo["Name"] ?></a></span>,
	<?            $lastStateHeading = $info["State"];
				} ?>
				  <a href="./countydetail.php?stateid=<?= $stateInfo["objectid"] ?>&county=<?= urlencode($info["County"]) ?>"><?= $info["County"] ?> County</a></td>
				</tr>
	<?		} ?>

			<tr>
				<td width="40%">
					<a href="./locationdetail.php?locationid=<?= $info["locationid"] ?>"><?= $info["LocationName"] ?></a>
				</td>

	<?		for ($index = 1; $index <= (1 + getLatestYear() - getEarliestYear()); $index++)
			{ ?>
				<td class=bordered align=center>
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
        SELECT distinct(LocationName), County, State, location.objectid AS locationid, bit_or(1 << month(TripDate)) AS mask " .
		  $this->getFromClause() . " " .
		  $this->getWhereClause() . " 
        GROUP BY sighting.LocationName
        ORDER BY location.State, location.County, location.Name;";

		$gridQuery = performQuery("Location By Month Query", $gridQueryString); ?>

		<table cellpadding=0 cellspacing=0 cols=11 class="report-content" width="100%">
		<tr><td></td><? insertMonthLabels() ?></tr>

	<?
		$prevInfo = "";
		while ($info = mysql_fetch_array($gridQuery))
		{
			$theMask = $info["mask"];

			if ($prevInfo == "" || $countyHeadingsOK && ($prevInfo["County"] != $info["County"])) {
				$stateInfo = getStateInfoForAbbreviation($info["State"]) ?>

				<tr><td class=subheading colspan=13>
	<?          if ($lastStateHeading != $info["State"]) { ?>
				  <span class="statename"><a href="./statedetail.php?stateid=<?= $stateInfo["objectid"] ?>"><?= $stateInfo["Name"] ?></a></span>,
	<?            $lastStateHeading = $info["State"];
				} ?>
				  <a href="./countydetail.php?stateid=<?= $stateInfo["objectid"] ?>&county=<?= urlencode($info["County"]) ?>"><?= $info["County"] ?> County</a></td>
				</tr>
	<?		} ?>

			<tr>
				<td>
					<a href="./locationdetail.php?locationid=<?= $info["locationid"] ?>"><?= $info["LocationName"] ?></a>
				</td>

	<?		for ($index = 1; $index <= 12; $index++)
			{ ?>
				<td class=bordered align=center>
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
			if ($countyHeadingsOK && $divideByCounties && (($prevInfo["State"] != $info["State"]) || ($prevInfo["County"] != $info["County"])))
			{ ?>
				<div class="subheading">
<?              if ($lastStateHeading != $info["State"])
				{
					$stateInfo = getStateInfoForAbbreviation($info["State"]); ?>
					<span class="statename"><a href="./statedetail.php?view=<?= $this->mReq->getView() ?>&stateid=<?= $stateInfo["objectid"]?>"><?= $stateInfo["Name"] ?></a></span>,
<?                  $lastStateHeading = $info["State"];
				} ?>
				<a href="./countydetail.php?view=<?= $this->mReq->getView() ?>&stateid=<?= $stateInfo["objectid"]?>&county=<?= $info["County"] ?>">
			      <?= $info["County"] ?> County
			    </a>
				</div>
<?          } // TODO, list below the county and state name if not dividing by county/state ?>

			<div>
			  <a href="./locationdetail.php?view=species&locationid=<?= $info["objectid"] ?>"><?= $info["Name"] ?></a>

<?          if ($info["locationPhotos"] > 0) { ?>
              <a href="./locationdetail.php?view=photo&locationid=<?= $info["objectid"] ?>">
			      <img border="0" align="bottom" src="./images/camera.gif" alt="photo">
			  </a>
<?           } ?>

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

