<?php

require_once("./birdwalkerquery.php");

class SpeciesQuery extends BirdWalkerQuery
{
	function SpeciesQuery($inReq)
	{
		$this->BirdWalkerQuery($inReq);
	}

	function getGroupByClause()
	{
		return "GROUP BY species.objectid";
	}

	function getSelectClause()
	{
		$selectClause = "SELECT DISTINCT species.objectid, species.CommonName, species.LatinName, species.ABACountable, sum(sighting.Photo) as speciesPhotos";

		if ($this->mReq->getTripID() == "")
		{
			$selectClause = $selectClause . ", min(sighting.Exclude) AS AllExclude";
		}

		if ($this->mReq->getTripID() != "")
		{
			$selectClause = $selectClause . ", sighting.Notes, sighting.Exclude, sighting.Photo, sighting.objectid AS sightingid";
		}
		else if (($this->mReq->getLocationID() != "") || ($this->mReq->getCounty() != "") || ($this->mReq->getStateID() != ""))
		{
			$selectClause = $selectClause . ",  min(concat(sighting.TripDate, lpad(sighting.objectid, 6, '0'))) as earliestsighting";
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
            FROM sighting, species" . $otherTables . " ";
	}


	function getWhereClause()
	{
		$whereClause = "WHERE species.Abbreviation=sighting.SpeciesAbbreviation";

		if ($this->mReq->getTripID() != "") {
			$tripInfo = $this->mReq->getTripInfo();
			$whereClause = $whereClause . " AND sighting.TripDate='" . $tripInfo["Date"] . "'";
		}

		if ($this->mReq->getLocationID() != "") {
			$whereClause = $whereClause . " AND location.objectid=" . $this->mReq->getLocationID();
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mReq->getCounty() != "") {
			$whereClause = $whereClause . " AND location.County='" . $this->mReq->getCounty() . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
			$whereClause = $whereClause . " AND location.State='" . $stateInfo["Abbreviation"] . "'";
			$whereClause = $whereClause . " AND location.Name=sighting.LocationName"; 
		}

		if ($this->mReq->getFamilyID() != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mReq->getFamilyID() * pow(10, 7) . " AND
              species.objectid < " . ($this->mReq->getFamilyID() + 1) * pow(10, 7);
		} elseif ($this->mReq->getOrderID() != "") {
			$whereClause = $whereClause . " AND
              species.objectid >= " . $this->mReq->getOrderID() * pow(10, 9) . " AND
              species.objectid < " . ($this->mReq->getOrderID() + 1) * pow(10, 9);
		}
		else
		{
			echo "<!-- where clause for species query -->";
			$this->mReq->debug();
		}

		if ($this->mReq->getMonth() !="") {
			$whereClause = $whereClause . " AND Month(TripDate)=" . $this->mReq->getMonth();
		}
		if ($this->mReq->getYear() !="") {
			$whereClause = $whereClause . " AND Year(TripDate)=" . $this->mReq->getYear();
		}


		return $whereClause;
	}

	function getSpeciesCount()
	{
		return performCount("Count Species",
          "SELECT COUNT(DISTINCT species.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function getPhotoCount()
	{
		return performCount("Count Species With Photos",
          "SELECT COUNT(DISTINCT species.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1' ORDER BY species.objectid");
	}

	function performQuery()
	{
		return performQuery("Query for Species",
          "SELECT DISTINCT species.objectid, species.* ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function getOneRandom()
	{
		return performOneRowQuery("Find one random bird with photo",
          "SELECT DISTINCT species.objectid, species.*, " . dailyRandomSeedColumn() . " " .
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1' ORDER BY shuffle LIMIT 1");
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
								$this->getGroupByClause() . " ORDER BY species.objectid");
		
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
				 $orderNum =  floor($info["objectid"] / pow(10, 9));
				 $temp = "";
				 array_key_exists("earliestsighting", $info) && $temp = $info["earliestsighting"];
				 $earliestsightingid = round(substr($temp, 10));
		
				 if ($divideByFamily && ($prevInfo == "" ||
					 (getFamilyIDFromSpeciesID($prevInfo["objectid"]) != getFamilyIDFromSpeciesID($info["objectid"]))))
				 { ?>
					 <div class="subheading"><?= getFamilyDetailLinkFromSpeciesID($info["objectid"]) ?></div>
<?               } ?>

		             <div><a href="./speciesdetail.php?speciesid=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a>
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
		             <a href="./speciesdetail.php?view=photo&speciesid=<?= $info["objectid"] ?>">
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

				 if (array_key_exists("Notes", $info) && strlen($info["Notes"]) > 0)
				 { ?>
					 <div class="sighting-notes"><?= $info["Notes"] ?></div><?
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
          "SELECT COUNT(DISTINCT species.objectid) AS count, year(sighting.TripDate) AS year " .
				$this->getFromClause() . " " .
				$this->getWhereClause() . "
			GROUP BY year");

		$gridQueryString="
          SELECT DISTINCT(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) AS mask " .
		      $this->getFromClause() . " " .
		      $this->getWhereClause() . " 
            GROUP BY sighting.SpeciesAbbreviation
            ORDER BY speciesid";

		$gridQuery = performQuery("Species By Year 2", $gridQueryString); ?>

		<table cellpadding=0 cellspacing=0 class="report-content" width="100%">
			<tr><td></td><? insertYearLabels() ?></tr>

	<?	$prevInfo = "";
		while ($info = mysql_fetch_array($gridQuery))
		{
			$theMask = $info["mask"];

			if ($prevInfo == "" || getFamilyIDFromSpeciesID($prevInfo["speciesid"]) != getFamilyIDFromSpeciesID($info["speciesid"]))
			{
				$taxoInfo = getFamilyInfoFromSpeciesID($info["speciesid"]); ?>
				<tr><td class=subheading colspan=11><?= strtolower($taxoInfo["LatinName"]) ?></td></tr>
	<?		} ?>

			<tr><td><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

	<?		for ($index = 1; $index <=  (1 + getLatestYear() - getEarliestYear()); $index++)
			{ ?>
				<td class=bordered align=center>

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
				<td class=bordered align=center>
	<?
					$clickRequest = new Request; // make a new request from current params and modify
					$clickRequest->setYear(1995 + $index);
					$clickRequest->setView("");
					$clickRequest->setPageScript("specieslist.php");
					echo $clickRequest->linkToSelf($info["count"]);
	?>
				</td>
	<?			$info = mysql_fetch_array($yearTotals);
			}
			else
			{ ?>
				<td class=bordered align=center>&nbsp;</td>
	<?		}
		} ?>

			</tr>

		</table>
	<?
	}

	/**
	 * Show a set of sightings, species by rows, months by columns.
	 */
	function formatSpeciesByMonthTable()
	{
		$monthTotals = performQuery("Species by Month Query 1",
          "SELECT COUNT(DISTINCT species.objectid) AS count, month(sighting.TripDate) AS month " .
				$this->getFromClause() . " " .
				$this->getWhereClause() . "
			GROUP BY month");

		$gridQueryString="
    SELECT DISTINCT(CommonName), species.objectid AS speciesid, bit_or(1 << month(TripDate)) AS mask " . 
		  $this->getFromClause() . " " .
		  $this->getWhereClause() . " 
      GROUP BY sighting.SpeciesAbbreviation
      ORDER BY speciesid";

		$gridQuery = performQuery("Species by Month Query 2", $gridQueryString); ?>

		<table cellpadding=0 cellspacing=0 class="report-content" width="100%">
			<tr><td></td><? insertMonthLabels() ?></tr>

	<?	
		$prevInfo = "";
		while ($info = mysql_fetch_array($gridQuery))
		{
			$theMask = $info["mask"];

			if ($prevInfo == "" || getFamilyIDFromSpeciesID($prevInfo["speciesid"]) != getFamilyIDFromSpeciesID($info["speciesid"]))
			{
				$taxoInfo = getFamilyInfoFromSpeciesID($info["speciesid"]); ?>
				<tr><td class="subheading" colspan="13"><?= strtolower($taxoInfo["LatinName"]) ?></td></tr>
	<?		} ?>

			<tr><td width="40%"><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

	<?		for ($index = 1; $index <= 12; $index++)
			{ ?>
				<td class=bordered align=center>

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
				<td class=bordered align=center>
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
				<td class=bordered align=center>&nbsp;</td>
	<?		}
		} ?>

			</tr>


		</table>
	<?
	}
}

?>
