<?php

function navigationHeader()
{
	echo "

<script type=\"text/javascript\">
function sightinglist(url) {
    var helpWindow = window.open(url, \"helpWindow\", \"width=500,height=300,status=yes,toolbar=yes,resizable=yes,scrollbars=yes,location=yes\");
    helpWindow.focus();
}
</script>

	<div class=\"contentleft\">
      <div style=\"height: 50px\"><a href=\"./index.php\">birdWalker</div>
	  <div class=\"leftsubtitle\"><a href=\"./tripindex.php\">Trips</a></div>
	  <div class=\"leftsubtitle\"><a href=\"./speciesindex.php\">Years</a></div>
	  <div class=\"leftsubtitle\"><a href=\"./chronolifelist.php\">Life List</a></div>
	  <div class=\"leftsubtitle\"><a href=\"./locationindex.php\">Locations</a></div>
	  <div class=\"leftsubtitle\"><a href=\"./countyindex.php\">Counties</a></div>
	  <div class=\"leftsubtitle\"><a href=\"./photoindextaxo.php\">Photos</a></div>";

	if (getEnableEdit())
	{
		echo "<br><div class=\"leftsubtitle\">";
		echo "<a href=\"./tripcreate.php\">Create Trip</a><br>";
		echo "<a href=\"./photosneeded.php\">Photos Needed</a><br>";
		echo "<a href=\"./errorcheck.php\">Errors</a>";
		echo "</div>";
	}

	echo "
    </div>
 ";
}

function navigationButtons($urlPrefix, $current, $first, $prev, $next, $last)
{
    echo "<div class=\"navigationleft\">";
	
	if ($current == $first)
	{
		echo "first prev";
	}
	else
	{
		echo "<a href=\"" . $urlPrefix . $first . "\">first</a> <a href=\"" . $urlPrefix . $prev . "\">prev</a>";
	}

	if ($current == $last)
	{
		echo " next last";
	}
	else
	{
		echo " <a href=\"" . $urlPrefix . $next . "\">next</a> <a href=\"" . $urlPrefix . $last . "\">last</a>";
	}

	echo "</div>";

}

//
// -------------------------- DATABASE UTILITIES -------------------------------
//

function getEnableEdit()
{
	return true;
}

/**
 * Prepare to query the birdwalker database.
 */
function selectDatabase()
{
	// MySQL info variables
	$mysql["host"] = "localhost";
	$mysql["user"] = "birdwalker";
	$mysql["pass"] = "birdwalker";
	$mysql["database"] = "birdwalker";

	// Connect to MySQL
	mysql_connect($mysql["host"], $mysql["user"], $mysql["pass"]) or die("MySQL connection failed. Some info is wrong");

	// Select database
	mysql_select_db($mysql["database"]) or die("Could not connect to DataBase");
}

function getPhotoFilename($sightingInfo)
{
	return $sightingInfo["TripDate"] . "-" . $sightingInfo["SpeciesAbbreviation"] . ".jpg";
}

function getPhotoLinkForSightingInfo($sightingInfo)
{
	echo " <a href=\"./photodetail.php?id=" . $sightingInfo["objectid"] . "\"><img align=center src=\"./images/camera.gif\"></a>";
}

function getPhotoURLForSightingInfo($sightingInfo)
{
	return "./images/photo/" . getPhotoFilename($sightingInfo);
}

function getThumbForSightingInfo($sightingInfo)
{
	$thumbFilename = getPhotoFilename($sightingInfo);

	list($width, $height, $type, $attr) = getimagesize("./images/thumb/" . $thumbFilename);

	return "<a href=\"./photodetail.php?id=" . $sightingInfo["objectid"] . "\"><img width=" . $width . " height=" . $height . " src=\"./images/thumb/" . $thumbFilename . "\" align=right border=0></a>";
}

/**
 * Select the birdwalker database, perform a query, die on error, return the query.
 */
function performQuery($queryString)
{
	selectDatabase();
	$theQuery = mysql_query($queryString) or die("<p>Error during query: " . $queryString . "</p><p>" . mysql_error() . "</p>");
	return $theQuery;
}

/**
 * Select the birdwalker database, perform a counting query, die on error, return the count.
 */
function performCount($queryString)
{
	selectDatabase();
	$theQuery = mysql_query($queryString) or die("count query error " . $queryString);
	$theCount = mysql_fetch_array($theQuery);
	return $theCount[0];
}

/**
 * Select the birdwalker database, perform a one row query, die on error, return the first row.
 */
function performOneRowQuery($queryString)
{
	selectDatabase();
	$theQuery = mysql_query($queryString) or die("single row query error " . $queryString);
	$theFirstRow = mysql_fetch_array($theQuery);
	return $theFirstRow;
}

function bitToString($aBitVector, $anIndex)
{
	if (($aBitVector >> $anIndex) & 1) { return "X"; } else { return "&nbsp;" ; }
}

//
// -------------------------- SIGHTINGS, FIRST -------------------------------
//


function getSightingInfo($objectid)
{
	return performOneRowQuery("SELECT * FROM sighting where objectid=" . $objectid);
}

/**
 * Find the first sighting for each species.
 */
function getFirstSightings()
{
	$firstSightings = null;


	performQuery("CREATE TEMPORARY TABLE tmp ( abbrev varchar(16) default NULL, tripdate date default NULL);");
	performQuery("INSERT INTO tmp SELECT SpeciesAbbreviation, MIN(TripDate) FROM sighting where Exclude!='1' GROUP BY SpeciesAbbreviation;");
	$firstSightingQuery = performQuery("SELECT sighting.objectid, tmp.tripdate FROM sighting, tmp WHERE sighting.SpeciesAbbreviation=tmp.abbrev and sighting.TripDate=tmp.tripdate order by tripdate;");

	while ($info = mysql_fetch_array($firstSightingQuery))
	{
		$firstSightingID = $info["objectid"];
		$firstSightingDate = $info["tripdate"];
		$firstSightings[$firstSightingID] = $firstSightingDate;
	}

	performQuery("DROP TABLE tmp;");

	return $firstSightings;
}

/**
 * Find the first sighting for each species in the given year
 */
function getFirstYearSightings($theYear)
{
	$firstSightings = null;

	performQuery("CREATE TEMPORARY TABLE tmp ( abbrev varchar(16) default NULL, tripdate date default NULL);");
	performQuery("INSERT INTO tmp SELECT SpeciesAbbreviation, MIN(TripDate) FROM sighting where Exclude!='1' and year(TripDate)='" . $theYear . "' GROUP BY SpeciesAbbreviation;");
	$firstSightingQuery = performQuery("SELECT sighting.objectid, tmp.tripdate FROM sighting, tmp WHERE sighting.SpeciesAbbreviation=tmp.abbrev and sighting.TripDate=tmp.tripdate order by tripdate;");

	while ($info = mysql_fetch_array($firstSightingQuery))
	{
		$firstSightingID = $info["objectid"];
		$firstSightingDate = $info["tripdate"];
		$firstSightings[$firstSightingID] = $firstSightingDate;
	}

	return $firstSightings;
}


//
// ---------------------------- SPECIES ---------------------------
//

/**
 * Select the birdwalker database, count species according to where clause, return the count.
 */
function getSpeciesCount($whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.Exclude!='1'")
{
	$speciesCountQueryString =
		"SELECT count(distinct species.objectid)
     FROM species, sighting
     where " . $whereClause;

	return performCount($speciesCountQueryString);
}

/**
 * Select the birdwalker database, query species according to where clause, return the query.
 */
function getSpeciesQuery($whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.Exclude!='1'", $orderClause = "species.objectid")
{
	$speciesQueryString =
		"SELECT distinct species.* FROM species, sighting
     where " . $whereClause . "
     order by " . $orderClause;

	$speciesQuery = performQuery($speciesQueryString);

	return $speciesQuery;
}

/**
 * Count species according to fancy query.
 */
function getFancySpeciesCount($whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation")
{
	$speciesCountQueryString =
		"SELECT count(distinct species.objectid)
     FROM species, sighting, location
     where " . $whereClause;

	return performCount($speciesCountQueryString);
}

/**
 * Select species according to fancy query.
 */
function getFancySpeciesQuery($whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation", $orderClause = "species.objectid", $additionalFields = "")
{
	$speciesQueryString =
		"SELECT distinct species.objectid, species.CommonName " . $additionalFields . "
     FROM species, sighting, location
     where " . $whereClause . "
     order by " . $orderClause;

	$speciesQuery = performQuery($speciesQueryString);

	return $speciesQuery;
}

/**
 * Get information about a specific species entry.
 */
function getSpeciesInfo($objectid)
{
	return performOneRowQuery("SELECT * FROM species where objectid=" . $objectid);
}

function insertYearLabels()
{
	for ($year = 1996; $year <= 2004; $year++)
	{
		echo "<td class=yearcell align=center><a href=\"./yeardetail.php?year=" . $year . "\">" . substr($year, 2, 2) . "</a></td>";
	}
}

/**
 * Show a set of sightings, species by rows, years by columns.
 */
function formatSpeciesByYearTable($gridQueryString, $extraSightingListParams)
{
	$gridQuery = performQuery($gridQueryString);

	echo "<table columns=11 cellpadding=0 cellspacing=0 class=\"report-content\" width=\"100%\">";

	echo "<tr><td></td>"; insertYearLabels(); echo "</tr>";

	while ($info = mysql_fetch_array($gridQuery))
	{
		$theMask = $info["mask"];

		if (getBestTaxonomyID($prevInfo["speciesid"]) != getBestTaxonomyID($info["speciesid"]))
		{
			$taxoInfo = getBestTaxonomyInfo($info["speciesid"]);
			echo "<tr><td class=titleblock colspan=11>" . $taxoInfo["LatinName"] . "</td></tr>";
		}

		echo "<tr><td class=firstcell><a href=\"./speciesdetail.php?id=" . $info["speciesid"] . "\">" . $info["CommonName"] . "</a></td> ";
		
		for ($index = 1; $index <= 9; $index++)
		{
			echo "<td class=bordered align=center>";
			if (($info["mask"] >> $index) & 1)
			{
				echo "<a href=\"./sightinglist.php?speciesid=" . $info["speciesid"] . "&year=" . (1995 + $index) . $extraSightingListParams . "\">X</a>";
			}
			else
			{
				echo "&nbsp;" ;
			}
			echo "</td>";
		}
		echo "</tr>";

		$prevInfo = $info;
		$reprintYears++;
	}

	echo "</table>";
}

/**
 * Show locations as rows, years as columns
 */
function formatLocationByYearTable($locationCount, $gridQueryString)
{
	$gridQuery = performQuery($gridQueryString);

	echo "<table cellpadding=0 cellspacing=0 columns=11 class=\"report-content\" width=\"100%\">";
	echo "<tr><td></td>"; insertYearLabels(); echo "</tr>";

	while ($info = mysql_fetch_array($gridQuery))
	{
		$theMask = $info["mask"];

		if ($prevInfo["County"] != $info["County"]) {
			echo "<tr><td class=titleblock colspan=11>" .  $info["County"] . " County, " . $info["State"] . "</td></tr>";
		}

		echo "<tr><td class=firstcell>";
		echo "<a href=\"./locationdetail.php?id=" . $info["locationid"] . "\">" . $info["LocationName"] . "</a>";
		echo "</td>";
		for ($index = 1; $index <= 9; $index++) echo "<td class=bordered align=center>" . bitToString($theMask, $index) . "</td>";
		echo "</tr>";

		$prevInfo = $info;
		$reprintYears++;
	}

	echo "</table>";
}

//
// ---------------------- TAXONOMY -------------------------
//

function getBestTaxonomyInfo($speciesid)
{
	return performOneRowQuery("select * from taxonomy where objectid='" . getBestTaxonomyID($speciesid) . "'");
}

function getBestTaxonomyID($speciesid)
{
	if ($speciesid >= 22000000000)
	{
		return floor($speciesid / pow(10,7)) * pow(10, 7);
	}
	else
	{
		return floor($speciesid / pow(10,9)) * pow(10, 9);
	}
}

/**
 * Get information about a specific order.
 */
function getOrderInfo($objectid)
{
	return getTaxonomyInfo($objectid, 9);
}

/**
 * Get information about a specific family.
 */
function getFamilyInfo($objectid)
{
	return getTaxonomyInfo($objectid, 7);
}

/**
 * Get information about a specific element of the taxonomy table by replacing digits with zeroes.
 */
function getTaxonomyInfo($objectid, $blankDigits)
{
	$shift = pow(10, $blankDigits - 1);
	$taxonomyID = floor($objectid / $shift) * $shift;
	$taxonomyQueryString = "SELECT * FROM taxonomy where objectid=" . $taxonomyID;

	return performOneRowQuery($taxonomyQueryString);
}

//
// ---------------------- TRIPS ------------------------
//

/**
 * Select the birdwalker database, query trips according to where clause, return the query.
 */
function getTripQuery($whereClause = "")
{
	if (strlen($whereClause) > 0)
	{
		$tripListQueryString = "select distinct trip.objectid, trip.*, date_format(Date, '%M %e, %Y') as niceDate, count(distinct sighting.SpeciesAbbreviation) as tripCount from trip, sighting where " . $whereClause . " group by trip.Date order by trip.Date desc";
	}
	else
	{
		$tripListQueryString = "select trip.*, date_format(Date, '%M %e, %Y') as niceDate, count(distinct sighting.SpeciesAbbreviation) as tripCount from trip, sighting where sighting.TripDate=trip.Date group by trip.Date order by trip.Date desc";
	}

	return performQuery($tripListQueryString);
}
/**
 * Select the birdwalker database, count trips according to where clause, return the count.
 */
function getTripCount($whereClause = "")
{
	if (strlen($whereClause) > 0)
	{
		$tripCountQueryString = "select count(distinct trip.objectid) from trip, sighting where " . $whereClause;
	}
	else
	{
		$tripCountQueryString = "select count(distinct trip.objectid) from trip";
	}

	return performCount($tripCountQueryString);
}

function getTripInfo($objectid)
{
	return performOneRowQuery("SELECT *, date_format(Date, '%W,  %M %e, %Y') as niceDate FROM trip where objectid=" . $objectid);
}



//
// ---------------------- LOCATIONS ------------------------
//

function getLocationInfo($objectid)
{
	return performOneRowQuery("SELECT * FROM location where objectid=" . $objectid);
}

function formatLocationList($locationListCount, $locationListQuery)
{
	$prevInfo=null;
	$divideByCounties = ($locationListCount > 20);

	while($info = mysql_fetch_array($locationListQuery))
	{
		if ($divideByCounties && (($prevInfo["State"] != $info["State"]) || ($prevInfo["County"] != $info["County"])))
		{
			echo "<div class=\"titleblock\">
              <a href=\"./countyindex.php?county=" . urlencode($info["County"]) . "\">" . $info["County"] . " County</a>,
              <a href=\"./stateindex.php?state=" . urlencode($info["State"]) . "\">" . $info["State"] . "</a></div>";
		}

		echo "<div class=firstcell><a href=\"./locationdetail.php?id=".$info["objectid"]."\">".$info["Name"]."</a></div>";
		$prevInfo = $info;   
	}
}


//
// ---------------------- MISC ------------------------
//

function getStateNameForAbbreviation($abbreviation)
{
	if ($abbreviation == "AZ") return "Arizona";
	else if ($abbreviation == "CA") return "California";
	else if ($abbreviation == "IA") return "Iowa";
	else if ($abbreviation == "IL") return "Illinois";
	else if ($abbreviation == "MA") return "Massachussets";
	else if ($abbreviation == "NJ") return "New Jersey";
	else if ($abbreviation == "OR") return "Oregon";
	else if ($abbreviation == "PA") return "Pennsylvania";
	else if ($abbreviation == "TX") return "Texas";
	else if ($abbreviation == "WI") return "Wisconsin";
	else return "Unknown";
}


?>
