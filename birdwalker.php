<?php
################################################
# birdWalker, Copyright 2006 William F Walker
#
# birdwalker.php -- general utility routines
################################################

function getIsLaptop()
{
 	$serverName = getenv("SERVER_NAME");
 	return ($serverName != "www.spflrc.org");
}

function getIsIPhone()
{
  return (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') != FALSE);
}

if (getIsLaptop()) {error_reporting(E_ALL);} else {error_reporting(E_ERROR);}

function htmlHead($title)
{
  if (getIsLaptop())
  {
	$title = "DEVELOPMENT " . $title;
  }

  if (getIsIPhone())
  {
	$title = "IPHONE " . $title;
  }

echo "<!DOCTYPE  HTML PUBLIC  \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
?>
<html>
  <head>
    <link rel="alternate" type="application/atom+xml" title="Atom" href="./indexrss.php" />
    <link rel="SHORTCUT ICON" href="./images/favicon.ico">
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $title ?></title>
  </head>

  <body>
	<div id="outer">
<?
}

function topRightBanner()
{ 
    $counter = rand(1, 4);
?>
    <div id="topright-home<?= $counter ?>">
	</div>
<?
}

function footer()
{
?>
    <div id="footer">
	  comments to <a href="mailto:walker@shout.net">walker@shout.net</a><br/>
		valid <a href="http://validator.w3.org/check/referer">XHTML 1.1</a>,
		<a href="http://jigsaw.w3.org/css-validator/check/referer">CSS 2.0</a><br>
    </div>
<?
}

function htmlFoot()
{
?>
    <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
    </script>
    <script type="text/javascript">
      _uacct = "UA-717974-1";
      urchinTracker();
    </script>
	</div>
  </body>
</html>
<?
}

function toggler($triangleID, $textID)
{
?>
    <span onclick='if (document.getElementById("<?= $triangleID ?>").src.match("closed.gif") == "closed.gif") { document.getElementById("<?= $triangleID ?>").src="./images/open.gif"; document.getElementById("<?= $textID ?>").style.display=""; } else { document.getElementById("<?= $triangleID ?>").src="./images/closed.gif";  document.getElementById("<?= $textID ?>").style.display="none"; }'><img id=<?= $triangleID ?> src="./images/closed.gif"/></span>
<?
}

function editLink($href)
{ 
    if (getEnableEdit()) { ?>
       <a href="<?= $href ?>"><img src="./images/edit.gif" border="0" alt="edit"></a>
<?  }
}

function pluralize($noun)
{
	if (substr( $noun, strlen( $noun ) - 7) == "species")
		return $noun;
	else
		return $noun . "s";
}


function countHeading($number, $name)
{ ?>
     <div class="heading"> <?
		echo $number . " "; if ($number == 1) echo $name; else echo pluralize($name); ?>
     </div> <?
}

function doubleCountHeading($number1, $name1, $number2, $name2)
{ ?>
     <div class="heading"> <?
		echo $number1 . " "; if ($number1 == 1) echo $name1; else echo pluralize($name1);
        if ($number2 != 0 ) { echo ", " . $number2 . " "; if ($number2 == 1) echo $name2; else echo pluralize($name2); }
?>   </div> <?
}

function browseButtons($pageKind, $urlPrefix, $currentID, $prevID, $prevName, $nextID, $nextName)
{
?>  <table width="100%" padding="0" spacing="0" cellpadding="0"><tr> <?

	if ($prevID == "")
	{
        ?><td width="40%" valign="top" class="prevlink">&lt; prev</td><?
	}
	else
	{
        ?><td width="40%" valign="top" class="prevlink"><a href="<?= $urlPrefix . $prevID ?>">&lt; prev <?= strtolower($prevName) ?></a></td><?
	}

    ?> <td width="20%" valign="top" class="pagekind"><?= $pageKind ?></td> <?

	if ($nextID == "")
	{
        ?><td width="40%" valign="top" class="nextlink">next &gt;</td><?
	}
	else
	{
        ?><td width="40%" valign="top" class="nextlink"><a href="<?= $urlPrefix . $nextID ?>"><?= strtolower($nextName) ?> next  &gt;</a></td><?
	}

?>  </tr></table> <?
}

function reference_url($info)
{
	if (strlen($info["reference_url"]) > 0)
	{
	  $url = $info["reference_url"];
	  $linkText="See also...";
	  if (substr_count($url, "http://www.mbr-pwrc.usgs.gov/") > 0)
      {
		$linkText="See also Patuxent Bird ID page...";
	  }
?>	  <a href="<?= $url ?>"><?= $linkText ?></a>
<?  }
}

function longNiceDateColumn($inDateColumnName = "date")
{
	return "date_format(" . $inDateColumnName . ", '%W,  %M %D, %Y') as niceDate";
}

function shortNiceDateColumn($inDateColumnName = "date")
{
	return "date_format(" . $inDateColumnName . ", '%M %D, %Y') as niceDate";
}

function dailyRandomSeedColumn()
{
	$localtimearray = localtime(time(), 1);
	$yearNum = $localtimearray["tm_year"];
	$dayNum = $localtimearray["tm_yday"];
	return "rand(" . ($yearNum * 366 + $dayNum) . ") AS shuffle";
}

function rightThumbnail($photoQueryString, $addLink)
{
	$photoQuery = performQuery("Right Thumbnail", $photoQueryString);

	if (mysql_num_rows($photoQuery) > 0)
	{
		$photoInfo = mysql_fetch_array($photoQuery);
		$filename = getPhotoFilename($photoInfo);

		$sizeAttributes = "";

		if ($addLink == true) { ?> <a href="./photodetail.php?sightingid=<?= $photoInfo["id"] ?>">  <? } ?>
           <img <?= $sizeAttributes ?> src="./images/thumb/<?= $filename ?>" width="100" height="100" border="0" align="right" class="inlinepict">
<?		if ($addLink == true) { ?> </a> <? }
	}
}

//
// -------------------------- DATABASE UTILITIES -------------------------------
//

function getEnableEdit()
{
  return getIsLaptop();
}

function getLogQueries()
{
  return getIsLaptop();
}

function getEarliestYear()
{
	return 1996;
}

function getLatestYear()
{
	return 2007;
}

/**
 * Prepare to query the birdwalker database.
 */
function selectDatabase()
{
	// MySQL info variables
	$mysql["database"] = "birdwalker";

 	if (getIsLaptop())
 	{
        $mysql["host"] = "127.0.0.1";
 		$mysql["user"] = "birdwalker";
 		$mysql["pass"] = "birdwalker";
 	}
 	else
 	{
        $mysql["host"] = "localhost";
 		$mysql["user"] = "walker";
 		$mysql["pass"] = "walker";
 	}

	// Connect to MySQL
	mysql_connect($mysql["host"], $mysql["user"], $mysql["pass"]) or die("MySQL connection failed. Some info is wrong (" . mysql_error() . ")");

	// Select database
	mysql_select_db($mysql["database"]) or die("Could not connect to DataBase");
}

function getPhotoFilename($sightingInfo)
{
	$tripInfo = getTripInfo($sightingInfo["trip_id"]);
	$speciesInfo = getSpeciesInfo($sightingInfo["species_id"]);
	return $tripInfo["date"] . "-" . $speciesInfo["abbreviation"] . ".jpg";
}

function getPhotoLinkForSightingInfo($sightingInfo, $fieldName="id")
{ ?>
	<a href="./photodetail.php?sightingid=<?= $sightingInfo[$fieldName] ?>"><img border="0" align="to"p src="./images/camera.gif" alt="photo"></a>
<?
}

function getPhotoURLForSightingInfo($sightingInfo)
{
	return "./images/photo/" . getPhotoFilename($sightingInfo);
}

function getThumbForSightingInfo($sightingInfo)
{
	$thumbFilename = getPhotoFilename($sightingInfo);

	return
		"<a href=\"./photodetail.php?sightingid=" . $sightingInfo["sightingid"] . "\">" .
		"<img  src=\"./images/thumb/" . $thumbFilename . "\" width=\"100\" height=\"100\" border=\"0\" alt=\"". $sightingInfo["common_name"] . "\">" . 
		"</a>";
}

function getmicrotime()
{
	list($usec, $sec) = explode(" ", microtime());
	return (float)$usec + (float)$sec;
}


/**
 * Select the birdwalker database, perform a query, die on error, return the query.
 */
function performQuery($inDescription, $inQueryString)
{
	if ($inDescription == "") die("Fatal error: Need description");

	$start = getmicrotime();
	selectDatabase();
	$theQuery = mysql_query($inQueryString) or die("<p>Error during query: " . $inQueryString . "</p><p>" . mysql_error() . "</p>");
	writeLog(round(getmicrotime() - $start, 3) . " seconds, " . $inDescription . " -- " . $inQueryString);

	return $theQuery;
}

function writeLog($message)
{
	if (getLogQueries())
	{
	  $fp = fopen("/tmp/birdwalker/sqlqueries.log", "a");
	  fwrite($fp, date("Y-m-d h:m:s -- ", time()) . $message . "\n\n");
	  fclose($fp);
	}
}

/**
 * Select the birdwalker database, perform a counting query, die on error, return the count.
 */
function performCount($inDescription, $queryString)
{
	$theQuery = performQuery($inDescription, $queryString);
	$theCount = mysql_fetch_array($theQuery);
	return $theCount[0];
}

/**
 * Select the birdwalker database, perform a one row query, die on error, return the first row.
 */
function performOneRowQuery($inDescription, $queryString, $errorChecking = true)
{
	$theQuery = performQuery($inDescription, $queryString);
	if ($errorChecking && mysql_num_rows($theQuery) > 1) die("Fatal error: BirdWalker Too Many Objects");
	if ($errorChecking && mysql_num_rows($theQuery) == 0) die("Fatal error: BirdWalker No Object Found");
	$theFirstRow = mysql_fetch_array($theQuery);
	return $theFirstRow;
}

//
// -------------------------- SIGHTINGS, FIRST -------------------------------
//

function getSightingInfo($id)
{
	return performOneRowQuery("Find sighting info",
			  "SELECT sightings.*, " . shortNiceDateColumn("trips.date") . " FROM sightings, trips where sightings.trip_id=trips.id AND sightings.id='" . $id . "'");
}

/**
 * Build a table called "tmp" containing first sighting for each species.
 * Caller is responsible for deleting the table.
 */

function buildFirstSightingsTable($whereClause)
{
	$firstSightings = null;

 	performQuery("Make tmp table",
	  "CREATE TEMPORARY TABLE tmp (
        species_id varchar(16) default NULL,
        trip_date date default NULL,
        id varchar(16) default NULL);");

	// here's what section 3.6.4 of the mysql manual calls:
	// "a quite inefficient trick called the MAX-CONCAT trick"
	// TODO upgrade to mysql 4.1 and use a subquery
	performQuery("Insert into tmp", 
      "INSERT INTO tmp
        SELECT species_id,
          LEFT(        MIN( CONCAT(trips.Date,lpad(sightings.id,6,'0')) ), 10) AS trip_date,
          0+SUBSTRING( MIN( CONCAT(trips.Date,lpad(sightings.id,6,'0')) ),  11) AS id
        FROM sightings, trips " .
        $whereClause . "
        GROUP BY species_id");
}

/**
 * Find the first sighting for each species.
 */
function getFirstSightings($whereClause="WHERE sightings.trip_id=trips.id AND Exclude!='1'")
{
	$firstSightings = null;

	buildFirstSightingsTable($whereClause);

	$firstSightingQuery = performQuery(
	  "Find First Sighting",
      "SELECT tmp.trip_date, tmp.id as id, tmp.species_id
        FROM tmp, species
        WHERE tmp.species_id=species.id AND species.aba_countable='1' 
        ORDER BY trip_date, species.id;");

	$index = 1;
	while ($info = mysql_fetch_array($firstSightingQuery))
	{
		$firstSightingID = $info["id"];
		$firstSightings[$firstSightingID] = $index;
		//		echo $index . $info["abbrev"] . "\n";
		$index++;
	}

	performQuery("Finish temp table", "DROP TABLE tmp;");

	return $firstSightings;
}

/**
 * Find the first sighting for each species in the given year
 */
function getFirstYearSightings($theYear)
{
	return getFirstSightings("WHERE sightings.trip_id=trips.id AND Exclude!='1' AND year(trips.Date)='" . $theYear . "'");
}


//
// ---------------------------- SPECIES ---------------------------
//

/**
 * Get information about a specific species entry.
 */
function getSpeciesInfo($id)
{
	return performOneRowQuery(
	    "Get species info",
		"SELECT *, (aba_countable=0) OR (char_length(Notes)>0) OR (char_length(reference_url)>0) AS noteworthy
         FROM species
         WHERE id='" . $id . "'");
}

function speciesBrowseButtons($url, $speciesID, $viewMode)
{
	$nextSpeciesID = performCount("Get Next Species ID",
    "SELECT min(species.id)
      FROM species, sightings
      WHERE sightings.species_id=species.id
      AND species.id>" . $speciesID . " LIMIT 1", false);

	if ($nextSpeciesID != "")
	{
		$nextSpeciesInfo = getSpeciesInfo($nextSpeciesID);
		$nextSpeciesLinkText = $nextSpeciesInfo["common_name"];
	}
	else
	{
		$nextSpeciesLinkText = "";
	}

	$prevSpeciesID = performCount("Get Previous Species ID",
    "SELECT max(species.id)
      FROM species, sightings
      WHERE sightings.species_id=species.id
      AND species.id<" . $speciesID . " LIMIT 1", false);

	if ($prevSpeciesID != "")
	{
		$prevSpeciesInfo = getSpeciesInfo($prevSpeciesID);
		$prevSpeciesLinkText = $prevSpeciesInfo["common_name"];
	}
	else
	{
		$prevSpeciesLinkText = "";
	}

	browseButtons("Species Detail", $url . "?view=" . $viewMode . "&speciesid=", $speciesID,
				  $prevSpeciesID, $prevSpeciesLinkText, $nextSpeciesID, $nextSpeciesLinkText);
}

//
// ---------------------- TAXONOMY -------------------------
//

function getFamilyInfoFromSpeciesID($speciesid)
{
	return performOneRowQuery("Get Family Info", "select * from taxonomy where id='" . getFamilyIDFromSpeciesID($speciesid) . "'");
}

function getFamilyIDFromSpeciesID($speciesid)
{
	return floor($speciesid / pow(10,7)) * pow(10, 7);
}

function getFamilyDetailLinkFromSpeciesID($speciesid, $viewMode="species")
{
	$taxoInfo = getFamilyInfoFromSpeciesID($speciesid);

	return "<a href=\"./familydetail.php?familyid=" . floor($speciesid / pow(10,7)) . "&view=" . $viewMode . "\">" .
		$taxoInfo["common_name"] . 
		"</a>";
}

/**
 * Get information about a specific order.
 */
function getOrderInfo($id)
{
	return getTaxonomyInfo($id, 10);
}

/**
 * Get information about a specific family.
 */
function getFamilyInfo($id)
{
	return getTaxonomyInfo($id, 7);
}

/**
 * Get information about a specific element of the taxonomy table by replacing digits with zeroes.
 */
function getTaxonomyInfo($id, $blankDigits)
{
	$shift = pow(10, $blankDigits - 1);
	$taxonomyID = floor($id / $shift) * $shift;
	$taxonomyQueryString = "SELECT * FROM taxonomy where id='" . $taxonomyID . "'";

	return performOneRowQuery("Get Taxonomy Info", $taxonomyQueryString);
}

//
// ---------------------- TRIPS ------------------------
//

function getTripInfo($id)
{
	return performOneRowQuery("Get Trip Info",
			  "SELECT *, Date as startTimestamp,  from_days(to_days(Date) + 1) as stopTimestamp, date_format(Date, '%W,  %M %D, %Y') as niceDate FROM trips where id='" . $id . "'");
}

function getTripInfoForDate($inDate)
{
	return performOneRowQuery("Get Trip Info for Date", 
              "SELECT *, date_format(Date, '%W,  %M %D, %Y') as niceDate FROM trips WHERE Date='" . $inDate . "'");
}

function tripBrowseButtons($url, $tripInfo, $viewMode)
{
	$nextTripInfo = performOneRowQuery("Get Next Trip", 
    "SELECT id, date_format(date, '%b %D, %Y') as niceDate FROM trips
      WHERE date > '" . $tripInfo["date"] . "'
      ORDER BY date LIMIT 1", false);
	$prevTripInfo = performOneRowQuery("Get Previous Trip", 
    "SELECT id, date_format(date, '%b %D, %Y') as niceDate FROM trips
      WHERE date < '" . $tripInfo["date"] . "'
      ORDER BY date DESC LIMIT 1", false);

	browseButtons("Trip Detail", $url . "?view=" . $viewMode . "&tripid=", $tripInfo["id"],
				  $prevTripInfo["id"], $prevTripInfo["niceDate"], $nextTripInfo["id"], $nextTripInfo["niceDate"]);
}

//
// ---------------------- LOCATIONS ------------------------
//

function getLocationInfo($id)
{
	return performOneRowQuery(
        "Get Location Info",
		"SELECT *, (char_length(Notes)>0) OR (char_length(reference_url)>0) AS noteworthy
         FROM locations where id='" . $id . "'");
}

function getLocationInfoForName($inLocationName)
{
	return performOneRowQuery(
        "Get Location Info for Name",
         "SELECT *, (char_length(Notes)>0) OR (char_length(reference_url)>0) AS noteworthy
          FROM locations WHERE name='" . $inLocationName . "'");
}

function locationBrowseButtons($url, $locationID, $viewMode)
{
	$siteInfo = getLocationInfo($locationID);

	$prevLocationInfo = performOneRowQuery("Get Previous Location", 
      "SELECT id, name FROM locations
        WHERE CONCAT(county_id,name) < '" . addslashes($siteInfo["county_id"] . $siteInfo["name"]) . "'
        ORDER BY CONCAT(county_id,name) DESC LIMIT 1", false);

	$nextLocationInfo = performOneRowQuery("Get Next Location", 
      "SELECT id, name FROM locations
        WHERE CONCAT(county_id,name) > '" . addslashes($siteInfo["county_id"] . $siteInfo["name"]) . "'
        ORDER BY CONCAT(county_id,name) LIMIT 1", false);

	browseButtons("Location Detail", $url . "?view=" . $viewMode . "&locationid=", $locationID,
				  $prevLocationInfo["id"], $prevLocationInfo["name"], $nextLocationInfo["id"], $nextLocationInfo["name"]);
}

function getStateInfo($id)
{
	return performOneRowQuery("Get State Info", "SELECT * FROM states where id='" . $id . "'");
}

function getCountyInfo($id)
{
	return performOneRowQuery("Get County Info", "SELECT * FROM counties where id='" . $id . "'");
}

function getStateInfoForAbbreviation($abbrev)
{
	return performOneRowQuery("Get State Info for Abbreviation", "SELECT * FROM states where abbreviation='" . $abbrev . "'");
}

function stateBrowseButtons($stateID, $viewMode)
{
	$nextStateID = performCount("Get Next State", 
    "SELECT min(states.id)
      FROM states, sightings, locations
      WHERE sightings.location_id=locations.id and states.id>" . $stateID . " LIMIT 1");

	if ($nextStateID != "")
	{
		$nextStateInfo = getStateInfo($nextStateID);
		$nextStateLinkText = $nextStateInfo["name"];
		$nextStateid = $nextStateInfo["id"];
	}
	else
	{
		$nextStateLinkText = "";
		$nextStateid = "";
	}

	$prevStateID = performCount("Get Previous State",
    "SELECT max(states.id)
      FROM states, sightings, locations
      WHERE sightings.location_id=locations.id AND states.id<" . $stateID . " LIMIT 1");

	if ($prevStateID != "" )
	{
		$prevStateInfo = getStateInfo($prevStateID);
		$prevStateLinkText = $prevStateInfo["name"];
		$prevStateid = $prevStateInfo["id"];
	}
	else
	{
		$prevStateLinkText = "";
		$prevStateid = "";
	}

	browseButtons("State Detail", "./statedetail.php?view=" . $viewMode . "&stateid=", $stateID,
				  $prevStateid, $prevStateLinkText, $nextStateid, $nextStateLinkText);
}

// -------------------------------------- TIME -----------------------------------
//

function insertYearLabels()
{
	for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	{ ?>
		<td class="yearcell" align="center"><a href="./yeardetail.php?year=<?= $year ?>"><?= $year ?></td>
<?	}
}

function insertMonthLabels()
{ ?>
    <td class="yearcell" align="center">Jan</td>
    <td class="yearcell" align="center">Feb</td>
    <td class="yearcell" align="center">Mar</td>
    <td class="yearcell" align="center">Apr</td>
    <td class="yearcell" align="center">May</td>
    <td class="yearcell" align="center">Jun</td>
    <td class="yearcell" align="center">Jul</td>
    <td class="yearcell" align="center">Aug</td>
    <td class="yearcell" align="center">Sep</td>
    <td class="yearcell" align="center">Oct</td>
    <td class="yearcell" align="center">Nov</td>
    <td class="yearcell" align="center">Dec</td>
<?
}

//
// ---------------------- MISC ------------------------
//

function getMonthNameForNumber($month)
{
	if ($month == 1) return "January";
	else if ($month == 2) return "February";
	else if ($month == 3) return "March";
	else if ($month == 4) return "April";
	else if ($month == 5) return "May";
	else if ($month == 6) return "June";
	else if ($month == 7) return "July";
	else if ($month == 8) return "August";
	else if ($month == 9) return "September";
	else if ($month == 10) return "October";
	else if ($month == 11) return "November";
	else if ($month == 12) return "December";
	else return "Unknown";
}

function getStateNameForAbbreviation($abbreviation)
{
	return performCount("Get State Name", "SELECT Name from states where Abbreviation='" . $abbreviation . "'");
}

function getValue($inName)
{
	if (array_key_exists($inName, $_GET)) return $_GET[$inName]; else return "";
}

function postValue($inName)
{
	if (array_key_exists($inName, $_POST)) return $_POST[$inName]; else return "";
}

?>
