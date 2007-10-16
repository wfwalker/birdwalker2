<?php

require_once("birdwalkerquery.php");

class SpeciesQuery extends BirdWalkerQuery
{
	function SpeciesQuery($inReq)
	{
		$this->BirdWalkerQuery($inReq);
	}

	function getGroupByClause()
	{
		return "GROUP BY species.id";
	}

	function getSelectClause()
	{
	  $selectClause = "SELECT DISTINCT species.id, species.CommonName, species.LatinName, species.ABACountable, SUM(sighting.Photo) as speciesPhotos, species.Notes as speciesNotes";

		if ($this->mReq->getTripID() == "")
		{
			$selectClause = $selectClause . ", min(sighting.Exclude) AS AllExclude";
		}

		if ($this->mReq->getTripID() != "")
		{
			$selectClause = $selectClause . ", sighting.Notes as sightingNotes, sighting.Exclude, sighting.Photo, sighting.id AS sightingid";
		}
		else if (($this->mReq->getLocationID() != "") || ($this->mReq->getCounty() != "") || ($this->mReq->getStateID() != ""))
		{
			$selectClause = $selectClause . ",  min(concat(sighting.trip_id, lpad(sighting.id, 6, '0'))) as earliestsighting";
		}


		return $selectClause;
	}

	function getFromClause()
	{
		$otherTables = "";

		if ($this->mReq->getLocationID() != "") {
			$otherTables = $otherTables . ", location";
		} elseif ($this->mReq->getCounty() != "") {
			$otherTables = $otherTables . ", location";
		} elseif ($this->mReq->getStateID() != "") {
			$otherTables = $otherTables . ", location";
		}

		return "
            FROM sighting, trip, species" . $otherTables . " ";
	}


	function getWhereClause()
	{
		$whereClause = "WHERE species.id=sighting.species_id AND sighting.trip_id=trip.id";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = $this->mReq->getTripInfo();
			$whereClause = $whereClause . " AND sighting.trip_id='" . $tripInfo["id"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND location.id=" . $this->mReq->getLocationID();
			$whereClause = $whereClause . " AND location.id=sighting.location_id"; 
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mReq->getCounty() . "'";
			$whereClause = $whereClause . " AND location.id=sighting.location_id"; 
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
			$whereClause = $whereClause . " AND location.id=sighting.location_id"; 
		}

		if ($this->mReq->getFamilyID() != "") {
			$whereClause = $whereClause . " AND
              species.id >= " . $this->mReq->getFamilyID() * pow(10, 7) . " AND
              species.id < " . ($this->mReq->getFamilyID() + 1) * pow(10, 7);
		} elseif ($this->mReq->getOrderID() != "") {
			$whereClause = $whereClause . " AND
              species.id >= " . $this->mReq->getOrderID() * pow(10, 9) . " AND
              species.id < " . ($this->mReq->getOrderID() + 1) * pow(10, 9);
		}

		if ($this->mReq->getDayOfMonth() !="") {
			$whereClause = $whereClause . " AND DayOfMonth(trip.Date)=" . $this->mReq->getDayOfMonth();
		}
		if ($this->mReq->getMonth() !="") {
			$whereClause = $whereClause . " AND Month(trip.Date)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(trip.Date)=" . $this->mReq->getYear();
		}


		return $whereClause;
	}

	function getSpeciesCount()
	{
		return performCount("Count Species",
          "SELECT COUNT(DISTINCT species.id) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.id");
	}

	function getPhotoCount()
	{
		return performCount("Count Species With Photos",
          "SELECT COUNT(DISTINCT species.id) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1' ORDER BY species.id");
	}

	function performQuery()
	{
		return performQuery("Query for Species",
          "SELECT DISTINCT species.id, species.* ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.id");
	}

	function getBirdOfTheDay($week)
	{
		return performOneRowQuery("Find one random bird with photo",
          "SELECT DISTINCT species.id, species.*, " . dailyRandomSeedColumn() . " " .
			$this->getFromClause() .
			$this->getWhereClause() . " AND trip.id=sighting.trip_id AND WEEK(trip.Date)= '" . $week . "' AND sighting.Photo='1' ORDER BY shuffle LIMIT 1");
	}

	/**
	 * Displays a list of species common names that result from a search over
	 * species and sighting tables.
	 */
	function formatTwoColumnSpeciesList($firstSightings = "", $firstYearSightings = "")
	{
		$dbQuery = performQuery("Two Column Species List Query",
								$this->getSelectClause() . " " .
								$this->getFromClause() . " " .
								$this->getWhereClause() . " " .
								$this->getGroupByClause() . " ORDER BY species.id");
		
		if ($firstSightings == "") $firstSightings = getFirstSightings();

		if ($firstYearSightings == "" && $this->mReq->getYear() != "")
		{
			$firstYearSightings = getFirstYearSightings($this->mReq->getYear());
		}

		
		$speciesCount = mysql_num_rows($dbQuery);

		doubleCountHeading($speciesCount, "species", $this->getPhotoCount(), "with photo");
			
		$divideByFamily = ($speciesCount > 30);
		$counter = round($speciesCount  * 0.52); ?>

	    <table width="100%" class="report-content">
		  <tr valign="top">
		    <td width="50%" class="leftcolumn">
<?
			 $prevInfo = "";
		     while($info = mysql_fetch_array($dbQuery))
			 {
				 $orderNum =  floor($info["id"] / pow(10, 9));
				 $temp = "";
				 array_key_exists("earliestsighting", $info) && $temp = $info["earliestsighting"];
				 $earliestsightingid = round(substr($temp, 10));
		
				 if ($divideByFamily && ($prevInfo == "" ||
					 (getFamilyIDFromSpeciesID($prevInfo["id"]) != getFamilyIDFromSpeciesID($info["id"]))))
				 { ?>
					 <div class="subheading"><?= getFamilyDetailLinkFromSpeciesID($info["id"]) ?></div>
<?               } ?>

		             <div><a href="./speciesdetail.php?speciesid=<?= $info["id"] ?>"><?= $info["CommonName"] ?></a>
<?
				 if (array_key_exists("sightingid", $info)) { editLink("./sightingedit.php?sightingid=" . $info["sightingid"]); }
				 if (array_key_exists("ABACountable", $info) && $info["ABACountable"] == "0") { echo "NOT ABA COUNTABLE"; }
				 if (array_key_exists("Exclude", $info) && $info["Exclude"] == "1") { echo "excluded"; }
				 if (array_key_exists("AllExclude", $info) && $info["AllExclude"] == "1") { echo "excluded"; }

				 if (array_key_exists("Photo", $info) && $info["Photo"] == "1")
				 {
					 echo getPhotoLinkForSightingInfo($info, "sightingid");
				 }
				 else if (array_key_exists("speciesPhotos", $info) && $info["speciesPhotos"]  > 0)
				 { ?>
		             <a href="./speciesdetail.php?view=photo&speciesid=<?= $info["id"] ?>">
						 <img border="0" valign="top" src="./images/camera.gif"/>
					 </a>
<?			     }

				 if ($this->mReq->getTripID() != "") 
				 {
					 if (array_key_exists("sightingid", $info) &&
						 array_key_exists($info["sightingid"], $firstSightings))
					 {
						 echo "life bird";
					 }
					 else if ($earliestsightingid != "" &&
							  array_key_exists($earliestsightingid, $firstSightings))
					 {
						 echo "life bird";
					 }
					 else if (array_key_exists("sightingid", $info) &&
							  $firstYearSightings != "" && array_key_exists($info["sightingid"], $firstYearSightings))
					 {
						 echo "year bird";
					 }
				 }

				 if (array_key_exists("sightingNotes", $info) && strlen($info["sightingNotes"]) > 0)
				 { ?>
				     <div class="sighting-notes"><?= stripslashes($info["sightingNotes"]) ?></div><?
				 } ?>

		    </div>

<?		    $prevInfo = $info;
		    $counter--;
		    if ($counter == 0)
		    { ?>
			    </td><td width="50%" class="rightcolumn">
<?		    }
	    } ?>

	    </td></tr></table>
<?
	}

	/**
	 * Show a set of sightings, species by rows, years by columns.
	 */
	function formatSpeciesByYearTable()
	{
		$yearTotals = performQuery("Species By Year 1",
          "SELECT COUNT(DISTINCT species.id) AS count, year(sighting.trip_id) AS year " .
				$this->getFromClause() . " " .
				$this->getWhereClause() . "
			GROUP BY year");

		$gridQueryString="
          SELECT DISTINCT(CommonName), species.id as speciesid, bit_or(1 << (year(trip.Date) - 1995)) AS mask " .
		      $this->getFromClause() . " " .
		      $this->getWhereClause() . " 
            GROUP BY sighting.species_id
            ORDER BY speciesid";

		$gridQuery = performQuery("Species By Year 2", $gridQueryString); ?>

		<div class="onecolumn">
		  <table cellpadding="0" cellspacing="0" class="report-content" width="100%">
			<tr><td></td><? insertYearLabels() ?></tr>

	<?	$prevInfo = "";
		while ($info = mysql_fetch_array($gridQuery))
		{
			$theMask = $info["mask"];

			if ($prevInfo == "" || getFamilyIDFromSpeciesID($prevInfo["speciesid"]) != getFamilyIDFromSpeciesID($info["speciesid"]))
			{
				$taxoInfo = getFamilyInfoFromSpeciesID($info["speciesid"]); ?>
				<tr><td colspan="11"><div class="subheading"><?= $taxoInfo["LatinName"] ?></div></td></tr>
	<?		} ?>

			<tr><td><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

	<?		for ($index = 1; $index <=  (1 + getLatestYear() - getEarliestYear()); $index++)
			{ ?>
				<td class="bordered" align="center">

	<?			if (($info["mask"] >> $index) & 1)
				{
					$clickRequest = new Request; // make a new request from current params and modify
					$clickRequest->setSpeciesID($info["speciesid"]);
					$clickRequest->setYear(1995 + $index);
					$clickRequest->setView("");
					$clickRequest->setPageScript("sightinglist.php");
					echo $clickRequest->linkToSelf("X");
				}
				else
				{ ?>
					&nbsp;
	<?			} ?>
				</td>
	<?		} ?>

			</tr>

	<?		$prevInfo = $info;
		} ?>

		<tr><td class="heading">total</td>

	<?	$info = mysql_fetch_array($yearTotals);
		for ($index = 1; $index <= (1 + getLatestYear() - getEarliestYear()); $index++)
		{
			if ($info["year"] == (getEarliestYear() - 1) + $index)
			{ ?>
				<td class="bordered" align="center">
	<?
					$clickRequest = new Request; // make a new request from current params and modify
					$clickRequest->setYear(1995 + $index);
					$clickRequest->setView("");
					$clickRequest->setPageScript("yeardetail.php");
					echo $clickRequest->linkToSelf($info["count"]);
	?>
				</td>
	<?			$info = mysql_fetch_array($yearTotals);
			}
			else
			{ ?>
				<td class="bordered" align="center">&nbsp;</td>
	<?		}
		} ?>

			</tr>

		</table>
	  </div>
	<?
	}

	/**
	 * Show a set of sightings, species by rows, months by columns.
	 */
	function formatSpeciesByMonthTable()
	{
		$monthTotals = performQuery("Species by Month Query 1",
          "SELECT COUNT(DISTINCT species.id) AS count, month(sighting.trip_id) AS month " .
				$this->getFromClause() . " " .
				$this->getWhereClause() . "
			GROUP BY month");

		$gridQueryString="
    SELECT DISTINCT(CommonName), species.id AS speciesid, bit_or(1 << month(trip.Date)) AS mask " . 
		  $this->getFromClause() . " " .
		  $this->getWhereClause() . " 
      GROUP BY sighting.species_id
      ORDER BY speciesid";

		$gridQuery = performQuery("Species by Month Query 2", $gridQueryString); ?>

	    <div class="onecolumn">
		  <table cellpadding="0" cellspacing="0" class="report-content" width="100%">
			<tr><td></td><? insertMonthLabels() ?></tr>

	<?	
		$prevInfo = "";
		while ($info = mysql_fetch_array($gridQuery))
		{
			$theMask = $info["mask"];

		    if ($prevInfo == "" || (getFamilyIDFromSpeciesID($prevInfo["speciesid"]) != getFamilyIDFromSpeciesID($info["speciesid"])))
			{ ?>
			    <tr><td colspan="13"><div class="subheading"><?= getFamilyDetailLinkFromSpeciesID($info["speciesid"]) ?></div></td></tr>
<?          } ?>

			<tr><td width="40%"><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

	<?		for ($index = 1; $index <= 12; $index++)
			{ ?>
				<td class="bordered" align="center">

	<?			if (($info["mask"] >> $index) & 1)
				{ 
					$clickRequest = new Request; // make a new request from current params and modify
					$clickRequest->setSpeciesID($info["speciesid"]);
					$clickRequest->setMonth($index);
					$clickRequest->setView("");
					$clickRequest->setPageScript("sightinglist.php");
					echo $clickRequest->linkToSelf("X");
				}
				else
				{ ?>
					&nbsp;
	<?			} ?>
				 </td>
	<?		} ?>

			</tr>

	<?		$prevInfo = $info;
		} ?>

		<tr><td class="heading">total</td>

	<?	$info = mysql_fetch_array($monthTotals);
		for ($index = 1; $index <= 12; $index++)
		{
			if ($info["month"] == $index)
			{ ?>
				<td class="bordered" align="center">
	<?
					$clickRequest = new Request; // make a new request from current params and modify
					$clickRequest->setMonth($index);
					$clickRequest->setView("");
					$clickRequest->setPageScript("specieslist.php");
					echo $clickRequest->linkToSelf($info["count"]);
	?>
				</td>
	<?			$info = mysql_fetch_array($monthTotals);
			}
			else
			{ ?>
				<td class="bordered" align="center">&nbsp;</td>
	<?		}
		} ?>

			</tr>


		</table>
	  </div>
	<?
	}
}

?>
