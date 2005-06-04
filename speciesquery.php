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
		if ($this->mReq->getTripID() != "")
		{
			return "";
		}
		else
		{
			return "GROUP BY species.objectid";
		} 
	}

	function getSelectClause()
	{
		$selectClause = "SELECT DISTINCT species.objectid, species.CommonName, species.LatinName, species.ABACountable";

		if ($this->mReq->getTripID() == "")
		{
			$selectClause = $selectClause . ", min(sighting.Exclude) AS AllExclude";
		}

		// TODO how can we get a sightingid into this select clause even if it's not a specific trip id?
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
			$tripInfo = getTripInfo($this->mReq->getTripID());
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
		return performCount("
          SELECT COUNT(DISTINCT species.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function getPhotoCount()
	{
		return performCount("
          SELECT COUNT(DISTINCT species.objectid) ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " AND sighting.Photo='1' ORDER BY species.objectid");
	}

	function performQuery()
	{
		echo "<!-- performQuery -->"; $this->mReq->debug();
		return performQuery("
          SELECT DISTINCT species.objectid, species.* ".
			$this->getFromClause() . " " .
			$this->getWhereClause() . " ORDER BY species.objectid");
	}

	function rightThumbnail()
	{
		rightThumbnail("
          SELECT sighting.*, " . dailyRandomSeedColumn() . " " .
			$this->getFromClause() . "  " .
			$this->getWhereClause() . "
            AND sighting.Photo='1'
            ORDER BY shuffle LIMIT 1", true);
	}

	/**
	 * Displays a list of species common names that result from a search over
	 * species and sighting tables.
	 */
	function formatTwoColumnSpeciesList($firstSightings = "", $firstYearSightings = "")
	{
		$dbQuery = performQuery(
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
		$divideByFamily = ($speciesCount > 30);
		$counter = round($speciesCount  * 0.52); ?>

	    <table width="100%" class=report-content>
		  <tr valign=top>
		    <td width="50%">
<?
			 $prevInfo = "";
		     while($info = mysql_fetch_array($dbQuery))
			 {
				 $orderNum =  floor($info["objectid"] / pow(10, 9));
				 $temp = "";
				 array_key_exists("earliestsighting", $info) && $temp = $info["earliestsighting"];
				 $earliestsightingid = round(substr($temp, 10));
		
				 if ($divideByFamily && ($prevInfo != "") &&
					 (getFamilyIDFromSpeciesID($prevInfo["objectid"]) != getFamilyIDFromSpeciesID($info["objectid"])))
				 { ?>
					 <div class=subheading><?= getFamilyDetailLinkFromSpeciesID($info["objectid"]) ?></div>
<?               } ?>

		             <div><a href="./speciesdetail.php?speciesid=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a>
<?
				 if (array_key_exists("sightingid", $info)) { editLink("./sightingedit.php?id=" . $info["sightingid"]); }
				 if (array_key_exists("Photo", $info) && $info["Photo"] == "1") { echo getPhotoLinkForSightingInfo($info, "sightingid"); }
				 if (array_key_exists("ABACountable", $info) && $info["ABACountable"] == "0") { echo "NOT ABA COUNTABLE"; }
				 if (array_key_exists("Exclude", $info) && $info["Exclude"] == "1") { echo "excluded"; }
				 if (array_key_exists("AllExclude", $info) && $info["AllExclude"] == "1") { echo "excluded"; }

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

				 if (array_key_exists("Notes", $info) && ($info["Notes"]) > 0)
				 { ?>
					 <div class="sighting-notes"><?= $info["Notes"] ?></div><?
				 } ?>

		    </div>

<?		    $prevInfo = $info;
		    $counter--;
		    if ($counter == 0)
		    { ?>
			    </td><td width="50%">
<?		    }
	    } ?>

	    </td></tr></table>
<?
	}

	function formatSpeciesByYearTable()
	{
		$annualTotal = performQuery("
          SELECT COUNT(DISTINCT species.objectid) AS count, year(sighting.TripDate) AS year " .
            $this->getFromClause() . " " .
		    $this->getWhereClause() . "
			GROUP BY year");

		formatSpeciesByYearTable($this, $annualTotal);
	}

	function formatSpeciesByMonthTable()
	{
		$monthlyTotal = performQuery("
          SELECT COUNT(DISTINCT species.objectid) AS count, month(sighting.TripDate) AS month " .
            $this->getFromClause() . " " .
		    $this->getWhereClause() . "
			GROUP BY month");

		formatSpeciesByMonthTable($this, $monthlyTotal);
	}

	function formatPhotos()
	{
		formatPhotos($this);
	}
}

?>
