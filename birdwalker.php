<?php

function navigationHeader()
{
  echo "

	<div class=\"contentleft\">
      <div style=\"height: 50px\"><a href=\"http://www.shout.net/~walker/\">birdWalker</div>
	  <div class=\"leftsubtitle\"><a href=\"./tripindex.php\">Trip Lists</a></div>
	  <div class=\"leftsubtitle\"><a href=\"./speciesindex.php\">Species Reports</a></div>
	  <div class=\"leftsubtitle\"><a href=\"./locationindex.php\">Location Reports</a></div>
	  <div class=\"leftsubtitle\"><a href=\"./countyindex.php\">County Reports</a></div>
	</div>
";
}

//
// -------------------------- DATABASE UTILITIES -------------------------------
//

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

/**
 * Select the birdwalker database, perform a query, die on error, return the query.
 */
function performQuery($queryString)
{
  selectDatabase();
  $theQuery = mysql_query($queryString) or die("query error " . $queryString);
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

function getFirstSightings()
{
	$firstSightings = null;

	$firstSightingQuery = performQuery("select objectid, min(TripDate) as firstDate from sighting where Exclude='0' group by SpeciesAbbreviation order by firstDate");

	while ($info = mysql_fetch_array($firstSightingQuery))
	{
		$firstSightingID = $info["objectid"];
		$firstSightingDate = $info["firstDate"];
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
function getSpeciesCount($whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.Exclude=0")
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
function getSpeciesQuery($whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.Exclude=0", $orderClause = "species.objectid")
{
  $speciesQueryString =
    "SELECT distinct species.objectid, species.CommonName " . $additionalFields . "
     FROM species, sighting
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
	for ($year = 1996; $year <= 2004; $year++) echo "<td class=yearcell align=center>" . substr($year, 2, 2) . "</td>";
}

/**
 * Show a list of species, subdivided by orders.
 */
function formatSpeciesList($speciesCount, $speciesQuery)
{
  $prevOrderNum = -1;
  $divideByOrders = ($speciesCount > 20);

  while($info = mysql_fetch_array($speciesQuery))
  {
	$orderNum =  floor($info["objectid"] / pow(10, 9));

	if ($divideByOrders && ($prevOrderNum != $orderNum))
    {
      $orderInfo = getOrderInfo($info["objectid"]);
      echo "<div class=\"titleblock\">" . $orderInfo["CommonName"] . "</div>";
    }

    echo "<div class=firstcell><a href=/~walker/birdwalker/speciesdetail.php?id=".$info["objectid"].">".$info["CommonName"]."</a></div>";
    $prevOrderNum = $orderNum;
  }
}

/**
 * Show a set of sightings, species by rows, years by columns.
 */
function formatSpeciesByYearTable($speciesCount, $gridQueryString)
{
	$gridQuery = performQuery($gridQueryString);
	$divideByOrders = ($speciesCount > 30);
	$reprintYears = 100;

	echo "<table columns=10 cellpadding=0 cellspacing=0 class=\"report-content\" width=\"100%\">";

	echo "<tr><td width=\"20%\"></td><td class=yearcell>Species</td>"; insertYearLabels(); echo "</tr>";

	while ($info = mysql_fetch_array($gridQuery))
	{
		$orderNum =  floor($info["speciesid"] / pow(10, 9));
		$theMask = $info["mask"];

		echo "<tr><td class=bordered>";

		if ($prevOrderNum != $orderNum) {
			$orderInfo = getOrderInfo($info["speciesid"]); echo $orderInfo["CommonName"];
		} else {
			echo "&nbsp;";
		}

		echo "</td>";
		echo "<td class=bordered><a href=\"./speciesdetail.php?id=" . $info["speciesid"] . "\">" . $info["CommonName"] . "</a></td> ";

		for ($index = 1; $index <= 9; $index++) echo "<td class=bordered align=center>" . bitToString($theMask, $index) . "</td>";
		echo "</tr>";

		$prevOrderNum = $orderNum;
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

	echo "<table cellpadding=0 cellspacing=0 columns=10 class=\"report-content\" width=\"100%\">";
	echo "<tr><td></td><td class=yearcell>Location</td>"; insertYearLabels(); echo "</tr>";

	while ($info = mysql_fetch_array($gridQuery))
	{
		$county = $info["County"];
		$theMask = $info["mask"];

		echo "<tr><td class=bordered>";

		if ($prevCounty != $county) {
			echo $county;
		} else {
			echo "&nbsp;";
		}

		echo "</td><td class=bordered><a href=\"./locationdetail.php?id=" . $info["locationid"] . "\">" . $info["LocationName"] . "</a></td> ";
		for ($index = 1; $index <= 9; $index++) echo "<td class=bordered align=center>" . bitToString($theMask, $index) . "</td>";
		echo "</tr>";

		$prevCounty = $county;
		$reprintYears++;
	}

	echo "</table>";
}

//
// ---------------------- TAXONOMY -------------------------
//

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

/**
 * Select the birdwalker database, query location according to where clause, return the query.
 */
function getLocationQuery($whereClause = "")
{
  if (strlen($whereClause) > 0)
  {
    $locationListQueryString = "select distinct(location.objectid), location.* from location, sighting where " . $whereClause . " order by State, County, Name";
  }
  else
  {
    $locationListQueryString = "select * from location order by State, County, Name";
  }

  return performQuery($locationListQueryString);
}

/**
 * Select the birdwalker database, count locations according to where clause, return the count.
 */
function getLocationCount($whereClause = "")
{
  if (strlen($whereClause) > 0)
  {
    $locationCountQueryString = "select count(distinct location.objectid) from location, sighting where " . $whereClause;
  }
  else
  {
    $locationCountQueryString = "select count(distinct location.objectid) from location";
  }

  return performCount($locationCountQueryString);
}

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
              <a href=\"/~walker/birdwalker/countyindex.php?county=" . urlencode($info["County"]) . "\">" . $info["County"] . " County</a>,
              <a href=\"/~walker/birdwalker/stateindex.php?state=" . urlencode($info["State"]) . "\">" . $info["State"] . "</a></div>";
    }

    echo "<div class=firstcell><a href=/~walker/birdwalker/locationdetail.php?id=".$info["objectid"].">".$info["Name"]."</a></div>";
    $prevInfo = $info;   
  }
}

?>
