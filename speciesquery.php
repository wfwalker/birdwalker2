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
	  $selectClause = "SELECT DISTINCT species.id, species.common_name, species.latin_name, species.aba_countable, SUM(sightings.Photo) as speciesPhotos, species.Notes as speciesNotes";

		if ($this->mReq->getTripID() == "")
		{
			$selectClause = $selectClause . ", min(sightings.exclude) AS AllExclude";
		}

		if ($this->mReq->getTripID() != "")
		{
			$selectClause = $selectClause . ", sightings.Notes as sightingNotes, sightings.Exclude, sightings.Photo, sightings.id AS sightingid";
		}
		else if (($this->mReq->getLocationID() != "") || ($this->mReq->getCounty() != "") || ($this->mReq->getStateID() != ""))
		{
			$selectClause = $selectClause . ",  min(concat(sightings.trip_id, lpad(sightings.id, 6, '0'))) as earliestsighting";
		}


		return $selectClause;
	}

	function getFromClause()
	{
		$otherTables = "";

		if ($this->mReq->getLocationID() != "") {
			$otherTables = $otherTables . ", locations";
		} elseif ($this->mReq->getCounty() != "") {
			$otherTables = $otherTables . ", locations";
		} elseif ($this->mReq->getStateID() != "") {
			$otherTables = $otherTables . ", locations";
		}

		return "
            FROM sightings, trips, species" . $otherTables . " ";
	}


	function getWhereClause()
	{
		$whereClause = "WHERE species.id=sightings.species_id AND sightings.trip_id=trips.id";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = $this->mReq->getTripInfo();
			$whereClause = $whereClause . " AND sightings.trip_id='" . $tripInfo["id"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND locations.id=" . $this->mReq->getLocationID();
			$whereClause = $whereClause . " AND locations.id=sightings.location_id"; 
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND locations.County='" . $this->mReq->getCounty() . "'";
			$whereClause = $whereClause . " AND locations.id=sightings.location_id"; 
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND locations.State='" . $stateInfo["Abbreviation"] . "'";
			$whereClause = $whereClause . " AND locations.id=sightings.location_id"; 
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
			$this->getWhereClause() . " AND sightings.Photo='1' ORDER BY species.id");
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
			$this->getWhereClause() . " AND trips.id=sightings.trip_id AND WEEK(trips.Date)= '" . $week . "' AND sightings.Photo='1' ORDER BY shuffle LIMIT 1");
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

		             <div><a href="./speciesdetail.php?speciesid=<?= $info["id"] ?>"><?= $info["common_name"] ?></a>
<?
				 if (array_key_exists("sightingid", $info)) { editLink("./sightingedit.php?sightingid=" . $info["sightingid"]); }
				 if (array_key_exists("aba_countable", $info) && $info["aba_countable"] == "0") { echo "NOT ABA COUNTABLE"; }
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
          "SELECT COUNT(DISTINCT species.id) AS count, year(sightings.trip_id) AS year " .
				$this->getFromClause() . " " .
				$this->getWhereClause() . "
			GROUP BY year");

		$gridQueryString="
          SELECT DISTINCT(common_name), species.id as speciesid, bit_or(1 << (year(trips.Date) - 1995)) AS mask " .
		      $this->getFromClause() . " " .
		      $this->getWhereClause() . " 
            GROUP BY sightings.species_id
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
				<tr><td colspan="11"><div class="subheading"><?= $taxoInfo["latin_name"] ?></div></td></tr>
	<?		} ?>

			<tr><td><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["common_name"] ?></a></td>

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
          "SELECT COUNT(DISTINCT species.id) AS count, month(sightings.trip_id) AS month " .
				$this->getFromClause() . " " .
				$this->getWhereClause() . "
			GROUP BY month");

		$gridQueryString="
    SELECT DISTINCT(common_name), species.id AS speciesid, bit_or(1 << month(trips.Date)) AS mask " . 
		  $this->getFromClause() . " " .
		  $this->getWhereClause() . " 
      GROUP BY sightings.species_id
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

			<tr><td width="40%"><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["common_name"] ?></a></td>

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
