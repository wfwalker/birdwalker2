<?php

error_reporting(E_ALL);

function globalMenu()
{ ?>
	<div class="contentleft">
      <div style="padding-bottom: 10px">
        <a href="http://wfwalker.blogspot.com/"><img src="./images/bill.jpg" border=0 alt="Bill"></a></div>
      <div style="padding-bottom: 10px">
        <a href="http://spinnity.blogspot.com/"><img src="./images/mary.jpg" border=0 alt="Mary"></a></div>
	  <div><a href="./tripindex.php">trips</a></div>
	  <div><a href="./speciesindex.php">birds</a></div>
	  <div><a href="./locationindex.php">locations</a></div>
	  <div><a href="./photoindextaxo.php">photos</a></div>
	  <div><a href="./speciesindex.php?view=chrono">life list</a></div>
      <div>&nbsp;</div>
	  <div><a href="./credits.php">about</a></div>
      <div>&nbsp;</div>
	  <div><a href="./slideshow.php" target="slideshow">slideshow</a></div>

<?	if (getEnableEdit())
	{ ?>
		<br><div>
		<a href="./tripcreate.php">create trip</a><br>
		<a href="./photosneeded.php">photo todo</a><br>
		<a href="./errorcheck.php">db todo</a><br>
		</div>
<?	} ?>

      <div>&nbsp;</div>
	  <div><a href="./indexrss.php">RSS</a></div>

    </div>
<?
}

function htmlHead($title)
{
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
<?
}

function footer()
{
?>
    <div class="footer">
	  comments to <a href="mailto:walker@shout.net">walker@shout.net</a><br/>
		valid <a href="http://validator.w3.org/check/referer">XHTML 1.1</a>, <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS 2.0</a><br>
    </div>
<?
}

function htmlFoot()
{
?>
  </body>
</html>
<?
}

function editLink($href)
{ 
    if (getEnableEdit()) { ?>
       <a href="<?= $href ?>"><img src="./images/edit.gif" border=0 alt="edit"></a>
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

function disabledBrowseButtons($pageKind)
{
	browseButtons($pageKind, "", 0, 0, 0, 0, 0);
}


function browseButtons($pageKind, $urlPrefix, $currentID, $prevID, $prevName, $nextID, $nextName)
{
?>  <table width="100%" class="pagesubtitle"><tr> <?

	if ($prevID == "")
	{
        ?><td width="33%"  class="prevlink">&lt; prev</td><?
	}
	else
	{
        ?><td width="33%" class="prevlink"><a href="<?= $urlPrefix . $prevID ?>">&lt; prev <?= strtolower($prevName) ?></a></td><?
	}

    ?> <td width="33%"  class="pagekind"><?= $pageKind ?></td> <?

	if ($nextID == "")
	{
        ?><td width="33%"  class="nextlink">next &gt;</td><?
	}
	else
	{
        ?><td width="33%"  class="nextlink"><a href="<?= $urlPrefix . $nextID ?>"><?= strtolower($nextName) ?> next  &gt;</a></td><?
	}

?>  </tr></table> <?
}

function referenceURL($info)
{
	if (strlen($info["ReferenceURL"]) > 0) { ?>
		<div class="viewlinks"><a href="<?= $info["ReferenceURL"] ?>">See also...</a></div>
<?  }
}

function niceDateColumn($inDateColumnName = "Date")
{
	return "date_format(" . $inDateColumnName . ", '%W,  %M %e, %Y') as niceDate";
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
	$photoQuery = performQuery($photoQueryString);

	if (mysql_num_rows($photoQuery) > 0)
	{
		$photoInfo = mysql_fetch_array($photoQuery);
		$filename = getPhotoFilename($photoInfo);
		list($width, $height, $type, $attr) = getimagesize("./images/thumb/" . $filename);

		$sizeAttributes = "";

		if ($width > 0) { $sizeAttributes = $sizeAttributes . " width=" . $width; }
		if ($height > 0) { $sizeAttributes = $sizeAttributes . "  height=" . $height; }

		if ($addLink == true) { ?> <a href="./photodetail.php?sightingid=<?= $photoInfo["objectid"] ?>">  <? } ?>
           <img <?= $sizeAttributes ?> src="./images/thumb/<?= $filename ?>" border=0 align="right" class="inlinepict">
<?		if ($addLink == true) { ?> </a> <? }
	}
}

function rightThumbnailAll()
{
	rightThumbnail("SELECT *, " . dailyRandomSeedColumn() . " FROM sighting WHERE Photo='1' ORDER BY shuffle LIMIT 1", true);
}

function navTrailPhotos($extra = "")
{
	$photoItems[] = "<a href=\"./photoindex.php\">photos</a>";
	navTrail(array_merge($photoItems, $extra));
}

function navTrailTrips($extra = "")
{
	$tripItems[] = "<a href=\"./tripindex.php\">trips</a>";
	navTrail(array_merge($tripItems, $extra));
}

function navTrail($extra = "")
{ ?>
	<div class="navigationright"><a href="./index.php">birdWalker</a>

<?
	if ($extra != "")
	{
		foreach ($extra as $item)
		{
			if (strlen($item) > 0)
			{ ?>
			    - <?= $item ?>
<?          }
	    }
    } ?>

    </div>
<?
}

//
// -------------------------- DATABASE UTILITIES -------------------------------
//

function getIsLaptop()
{
 	$serverName = getenv("SERVER_NAME");
 	return ($serverName == "127.0.0.1") || ($serverName == "localhost") || ($serverName == "") || ($serverName == "vermillion.local");
}

function getEnableEdit()
{
	return getIsLaptop();
}

function getEarliestYear()
{
	return 1996;
}

function getLatestYear()
{
	return 2005;
}

/**
 * Prepare to query the birdwalker database.
 */
function selectDatabase()
{
	// MySQL info variables
	$mysql["host"] = "localhost";
	$mysql["database"] = "birdwalker";

 	if (getIsLaptop())
 	{
 		$mysql["user"] = "birdwalker";
 		$mysql["pass"] = "birdwalker";
 	}
 	else
 	{
 		$mysql["user"] = "walker";
 		$mysql["pass"] = "walker";
 	}

	// Connect to MySQL
	mysql_connect($mysql["host"], $mysql["user"], $mysql["pass"]) or die("MySQL connection failed. Some info is wrong");

	// Select database
	mysql_select_db($mysql["database"]) or die("Could not connect to DataBase");
}

function getPhotoFilename($sightingInfo)
{
	return $sightingInfo["TripDate"] . "-" . $sightingInfo["SpeciesAbbreviation"] . ".jpg";
}

function getPhotoLinkForSightingInfo($sightingInfo, $fieldName="objectid")
{ ?>
	<a href="./photodetail.php?sightingid=<?= $sightingInfo[$fieldName] ?>"><img border=0 align=center src="./images/camera.gif" alt="photo"></a>
<?
}

function getPhotoURLForSightingInfo($sightingInfo)
{
	return "./images/photo/" . getPhotoFilename($sightingInfo);
}

function getThumbForSightingInfo($sightingInfo)
{
	$thumbFilename = getPhotoFilename($sightingInfo);

	list($width, $height, $type, $attr) = getimagesize("./images/thumb/" . $thumbFilename);

	$sizeAttributes = "";

	if ($width != "") { $sizeAttributes = $sizeAttributes . "  width=" . $width; }
	if ($height != "") { $sizeAttributes = $sizeAttributes . "  height=" . $height; }

	return
		"<a href=\"./photodetail.php?sightingid=" . $sightingInfo["sightingid"] . "\">" .
		"<img " . $sizeAttributes . " src=\"./images/thumb/" . $thumbFilename . "\" border=0 alt=\"bird\">" . 
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
function performQuery($inQueryString, $inDescription = "NEED DESC")
{
	$start = getmicrotime();
	selectDatabase();
	$theQuery = mysql_query($inQueryString) or die("<p>Error during query: " . $inQueryString . "</p><p>" . mysql_error() . "</p>");
	if (getEnableEdit())
	{ ?>

<!-- <?= round(getmicrotime() - $start, 3) ?> seconds, <?= $inDescription ?>: <?= $inQueryString ?> -->
<?	}
	return $theQuery;
}

/**
 * Select the birdwalker database, perform a counting query, die on error, return the count.
 */
function performCount($queryString)
{
	$theQuery = performQuery($queryString);
	$theCount = mysql_fetch_array($theQuery);
	echo "<!-- count = " . $theCount[0] . " -->";
	return $theCount[0];
}

/**
 * Select the birdwalker database, perform a one row query, die on error, return the first row.
 */
function performOneRowQuery($queryString, $errorChecking = true)
{
	$theQuery = performQuery($queryString);
	if ($errorChecking && mysql_num_rows($theQuery) > 1) die("Fatal error: BirdWalker Too Many Objects");
	if ($errorChecking && mysql_num_rows($theQuery) == 0) die("Fatal error: BirdWalker No Object Found");
	$theFirstRow = mysql_fetch_array($theQuery);
	return $theFirstRow;
}

//
// -------------------------- SIGHTINGS, FIRST -------------------------------
//

function getSightingInfo($objectid)
{
	return performOneRowQuery("SELECT *, " . niceDateColumn("TripDate") . " FROM sighting where objectid='" . $objectid . "'");
}

/**
 * Build a table called "tmp" containing first sighting for each species.
 * Caller is responsible for deleting the table.
 */

function buildFirstSightingsTable($whereClause)
{
	$firstSightings = null;

 	performQuery("CREATE TEMPORARY TABLE tmp (
        SpeciesAbbreviation varchar(16) default NULL,
        TripDate date default NULL,
        objectid varchar(16) default NULL);");

	// here's what section 3.6.4 of the mysql manual calls:
	// "a quite inefficient trick called the MAX-CONCAT trick"
	// TODO upgrade to mysql 4.1 and use a subquery
	performQuery("
      INSERT INTO tmp
        SELECT SpeciesAbbreviation,
          LEFT(        MIN( CONCAT(TripDate,lpad(objectid,6,'0')) ), 10) AS TripDate,
          0+SUBSTRING( MIN( CONCAT(TripDate,lpad(objectid,6,'0')) ),  11) AS objectid
        FROM sighting " .
        $whereClause . "
        GROUP BY SpeciesAbbreviation");
}

/**
 * Find the first sighting for each species.
 */
function getFirstSightings($whereClause="WHERE Exclude!='1'")
{
	$firstSightings = null;

	buildFirstSightingsTable($whereClause);

	$firstSightingQuery = performQuery("
      SELECT tmp.TripDate, tmp.objectid as objectid, tmp.SpeciesAbbreviation
        FROM tmp, species
        WHERE tmp.SpeciesAbbreviation=species.Abbreviation AND species.ABACountable='1' 
        ORDER BY tripdate, species.objectid;");

	$index = 1;
	while ($info = mysql_fetch_array($firstSightingQuery))
	{
		$firstSightingID = $info["objectid"];
		$firstSightings[$firstSightingID] = $index;
		//		echo $index . $info["abbrev"] . "\n";
		$index++;
	}

	performQuery("DROP TABLE tmp;");

	return $firstSightings;
}

/**
 * Find the first sighting for each species in the given year
 */
function getFirstYearSightings($theYear)
{
	return getFirstSightings("WHERE Exclude!='1' AND year(TripDate)='" . $theYear . "'");
}


//
// ---------------------------- SPECIES ---------------------------
//

/**
 * Get information about a specific species entry.
 */
function getSpeciesInfo($objectid)
{
	return performOneRowQuery("SELECT * FROM species where objectid='" . $objectid . "'");
}

function speciesBrowseButtons($url, $speciesID, $viewMode)
{
	$nextSpeciesID = performCount("
    SELECT min(species.objectid)
      FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid>" . $speciesID . " LIMIT 1", false);

	if ($nextSpeciesID != "")
	{
		$nextSpeciesInfo = getSpeciesInfo($nextSpeciesID);
		$nextSpeciesLinkText = $nextSpeciesInfo["CommonName"];
	}
	else
	{
		$nextSpeciesLinkText = "";
	}

	$prevSpeciesID = performCount("
    SELECT max(species.objectid)
      FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid<" . $speciesID . " LIMIT 1", false);

	if ($prevSpeciesID != "")
	{
		$prevSpeciesInfo = getSpeciesInfo($prevSpeciesID);
		$prevSpeciesLinkText = $prevSpeciesInfo["CommonName"];
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
	return performOneRowQuery("select * from taxonomy where objectid='" . getFamilyIDFromSpeciesID($speciesid) . "'");
}

function getFamilyIDFromSpeciesID($speciesid)
{
	return floor($speciesid / pow(10,7)) * pow(10, 7);
}

function getFamilyDetailLinkFromSpeciesID($speciesid)
{
	$taxoInfo = getFamilyInfoFromSpeciesID($speciesid);

	return "<a href=\"./familydetail.php?familyid=" . floor($speciesid / pow(10,7)) . "\">" .
		$taxoInfo["LatinName"] . 
		"</a>";
}

/**
 * Get information about a specific order.
 */
function getOrderInfo($objectid)
{
	return getTaxonomyInfo($objectid, 10);
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
	$taxonomyQueryString = "SELECT * FROM taxonomy where objectid='" . $taxonomyID . "'";

	return performOneRowQuery($taxonomyQueryString);
}

//
// ---------------------- TRIPS ------------------------
//

function getTripInfo($objectid)
{
	return performOneRowQuery("SELECT *, date_format(Date, '%W,  %M %e, %Y') as niceDate FROM trip where objectid='" . $objectid . "'");
}

function getTripInfoForDate($inDate)
{
	return performOneRowQuery("SELECT *, date_format(Date, '%W,  %M %e, %Y') as niceDate FROM trip where Date='" . $inDate . "'");
}

function tripBrowseButtons($url, $tripID, $viewMode)
{
	$tripInfo = getTripInfo($tripID);

	$nextTripInfo = performOneRowQuery("
    SELECT objectid, " . niceDateColumn() . " FROM trip
      WHERE Date > '" . $tripInfo["Date"] . "'
      ORDER BY Date LIMIT 1", false);
	$prevTripInfo = performOneRowQuery("
    SELECT objectid, " . niceDateColumn() . " FROM trip
      WHERE Date < '" . $tripInfo["Date"] . "'
      ORDER BY Date DESC LIMIT 1", false);

	browseButtons("Trip Detail", $url . "?view=" . $viewMode . "&tripid=", $tripID,
				  $prevTripInfo["objectid"], $prevTripInfo["niceDate"], $nextTripInfo["objectid"], $nextTripInfo["niceDate"]);
}

//
// ---------------------- LOCATIONS ------------------------
//

function getLocationInfo($objectid)
{
	return performOneRowQuery("SELECT * FROM location where objectid='" . $objectid . "'");
}

function getLocationInfoForName($inLocationName)
{
	return performOneRowQuery("SELECT * FROM location WHERE Name='" . $inLocationName . "'");
}

function locationBrowseButtons($url, $locationID, $viewMode)
{
	$siteInfo = getLocationInfo($locationID);

	$prevLocationInfo = performOneRowQuery("
      SELECT objectid, Name FROM location
        WHERE CONCAT(State,County,Name) < '" . $siteInfo["State"] . $siteInfo["County"] . $siteInfo["Name"] . "'
        ORDER BY CONCAT(State,County,Name) DESC LIMIT 1", false);

	$nextLocationInfo = performOneRowQuery("
      SELECT objectid, Name FROM location
        WHERE CONCAT(State,County,Name) > '" . $siteInfo["State"] . $siteInfo["County"] . $siteInfo["Name"] . "'
        ORDER BY CONCAT(State,County,Name) LIMIT 1", false);

	browseButtons("Location Detail", $url . "?view=" . $viewMode . "&locationid=", $locationID,
				  $prevLocationInfo["objectid"], $prevLocationInfo["Name"], $nextLocationInfo["objectid"], $nextLocationInfo["Name"]);
}

function getStateInfo($id)
{
	return performOneRowQuery("SELECT * FROM state where objectid='" . $id . "'");
}

function getStateInfoForAbbreviation($abbrev)
{
	return performOneRowQuery("SELECT * FROM state where Abbreviation='" . $abbrev . "'");
}

function stateBrowseButtons($stateID, $viewMode)
{
	$nextStateID = performCount("
    SELECT min(state.objectid)
      FROM state, sighting, location
      WHERE sighting.LocationName=location.Name AND location.State=state.Abbreviation and state.objectid>" . $stateID . " LIMIT 1");

	if ($nextStateID != "")
	{
		$nextStateInfo = getStateInfo($nextStateID);
		$nextStateLinkText = $nextStateInfo["Name"];
		$nextStateObjectID = $nextStateInfo["objectid"];
	}
	else
	{
		$nextStateLinkText = "";
		$nextStateObjectID = "";
	}

	$prevStateID = performCount("
    SELECT max(state.objectid)
      FROM state, sighting, location
      WHERE sighting.LocationName=location.Name AND location.State=state.Abbreviation and state.objectid<" . $stateID . " LIMIT 1");

	if ($prevStateID != "" )
	{
		$prevStateInfo = getStateInfo($prevStateID);
		$prevStateLinkText = $prevStateInfo["Name"];
		$prevStateObjectID = $prevStateInfo["objectid"];
	}
	else
	{
		$prevStateLinkText = "";
		$prevStateObjectID = "";
	}

	browseButtons("State Detail", "./statedetail.php?view=" . $viewMode . "&stateid=", $stateID,
				  $prevStateObjectID, $prevStateLinkText, $nextStateObjectID, $nextStateLinkText);
}

function rightThumbnailSpecies($abbrev)
{
	rightThumbnail(
    "SELECT sighting.*, " . dailyRandomSeedColumn() . "
      FROM sighting
      WHERE sighting.Photo='1' AND sighting.SpeciesAbbreviation='" . $abbrev . "'
      ORDER BY shuffle
      LIMIT 1",
	  true);
}

function rightThumbnailCounty($countyName)
{
	rightThumbnail(
    "SELECT sighting.*, " . dailyRandomSeedColumn() . "
      FROM sighting, location
      WHERE sighting.Photo='1' AND sighting.LocationName=location.Name AND location.County='" . $countyName . "'
      ORDER BY shuffle
      LIMIT 1",
      true);
}

function rightThumbnailState($stateCode)
{
	rightThumbnail(
      "SELECT sighting.*, " . dailyRandomSeedColumn() . "
        FROM sighting, location
        WHERE sighting.Photo='1' AND sighting.LocationName=location.Name AND location.State='" . $stateCode . "'
        ORDER BY shuffle LIMIT 1",
        true);
}

function rightThumbnailLocation($locationName)
{
  rightThumbnail("
    SELECT *, " . dailyRandomSeedColumn() . "
      FROM sighting
      WHERE Photo='1' AND LocationName='" . $locationName . "'
      ORDER BY shuffle LIMIT 1",
      true);
}

// -------------------------------------- TIME -----------------------------------
//

function insertYearLabels()
{
	for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	{ ?>
		<td class=yearcell align=center><a href="./yeardetail.php?year=<?= $year ?>"><?= $year ?></td>
<?	}
}

function insertMonthLabels()
{ ?>
    <td class=yearcell align=center>Jan</td>
    <td class=yearcell align=center>Feb</td>
    <td class=yearcell align=center>Mar</td>
    <td class=yearcell align=center>Apr</td>
    <td class=yearcell align=center>May</td>
    <td class=yearcell align=center>Jun</td>
    <td class=yearcell align=center>Jul</td>
    <td class=yearcell align=center>Aug</td>
    <td class=yearcell align=center>Sep</td>
    <td class=yearcell align=center>Oct</td>
    <td class=yearcell align=center>Nov</td>
    <td class=yearcell align=center>Dec</td>
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
	return performCount("SELECT Name from state where Abbreviation='" . $abbreviation . "'");
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
