<?php

function globalMenu()
{ ?>
	<div class="contentleft">
      <p><img src="./images/bill.jpg"></p>
      <p><img src="./images/mary.jpg"></p>
	  <div><a href="./tripindex.php">trips</a></div>
	  <div><a href="./speciesindex.php">birds</a></div>
	  <div><a href="./locationindex.php">locations</a></div>
	  <div><a href="./photoindex.php">photos</a></div>
	  <div><a href="./credits.php">about</a></div>
      <div>&nbsp;</div>
	  <div><a href="./slideshow.php">slideshow</a></div>
	  <div><a href="./chronolifelist.php">life list</a></div>

<?	if (getEnableEdit())
	{ ?>
		<br><div>
		<a href="./tripcreate.php">create trip</a><br>
		<a href="./photosneeded.php">photos needed</a><br>
		<a href="./errorcheck.php">error check</a><br>
		</div>
<?	} ?>

    </div>
<?
}

function param($getParams, $paramName, $defaultValue)
{
	if ($getParams[$paramName] != "")
		return $getParams[$paramName];
	else
		return $defaultValue;
}

function editLink($href)
{ 
    if (getEnableEdit()) { ?>
       <a href="<?= $href ?>"><img src="./images/edit.gif" border=0></a>
<?  }
}

function pluralize($noun)
{
	if ($noun == "species")
		return "species";
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

function disabledBrowseButtons()
{
	browseButtons("", 0, 0, 0, 0, 0);
}


function browseButtons($urlPrefix, $current, $first, $prev, $next, $last)
{ ?>
   <div class="navigationleft">

<?	if ($current == $first)
	{ ?>
        <img name="first" border="0" src="./images/first.gif" alt="prev"/>
        <img name="prev" border="0" src="./images/prev.gif" alt="prev"/>
<?	}
	else
	{ ?>
        <a href="<?= $urlPrefix . $first ?>"><img name="first" border="0" src="./images/first_hilite.gif" alt="first"/></a>
        <a href="<?= $urlPrefix . $prev ?>"><img name="prev" border="0" src="./images/prev_hilite.gif" alt="prev"/></a>
<?	}

	if ($current == $last)
	{?>
        <img name="next" border="0" src="./images/next.gif" alt="prev"/>
        <img name="last" border="0" src="./images/last.gif" alt="prev"/>
<?	}
	else
	{ ?> 
        <a href="<?= $urlPrefix . $next ?>"><img name="next" border="0" src="./images/next_hilite.gif" alt="next"/></a>
        <a href="<?= $urlPrefix . $last ?>"><img name="last" border="0" src="./images/last_hilite.gif" alt="last"/></a>
<?	} ?>
	</div>
<?
}

function referenceURL($info)
{
	if (strlen($info["ReferenceURL"]) > 0) { ?>
		<div><a href="<?= $info["ReferenceURL"] ?>">See also...</a></div>
<? }
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

		if ($addLink == true) { ?> <a href="./photodetail.php?id=<?= $photoInfo["objectid"] ?>">  <? } ?>
           <img <?= $sizeAttributes ?> src="./images/thumb/<?= $filename ?>" border=0 align="left" class="inlinepict">
<?		if ($addLink == true) { ?> </a> <? }
	}
}

function rightThumbnailAll()
{
	rightThumbnail("SELECT *, rand() AS shuffle FROM sighting WHERE Photo='1' ORDER BY shuffle LIMIT 1", true);
}

function formatPhotos($query)
{
	// TODO, the labels here should show values not fixed by the query!
	$dbQuery = $query->getPhotos();

    countHeading(mysql_num_rows($dbQuery), "photo");

	while ($sightingInfo = mysql_fetch_array($dbQuery))
	{
		$tripInfo = getTripInfoForDate($sightingInfo["TripDate"]);
		$tripYear =  substr($tripInfo["Date"], 0, 4);
		$locationInfo = getLocationInfoForName($sightingInfo["LocationName"]); ?>

        <div class=heading>
          <div class=pagesubtitle>
            <a href="./tripdetail.php?id=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["niceDate"] ?></a>
<?          editLink("./sightingedit.php?id=" . $sightingInfo["objectid"]); ?>
          </div>
          <div class=metadata>
            <a href="./locationdetail.php?id=<?= $locationInfo["objectid"] ?>"><?= $locationInfo["Name"] ?></a>
          </div>
        </div>

<?	    if ($sightingInfo["Photo"] == "1") {
			$photoFilename = getPhotoFilename($sightingInfo);

			list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

			<img width=<?= $width ?> height=<?= $height ?> src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>">
<?      }
	}
}

function navTrailBirds($extra = "")
{
    $birdItems[] = "<a href=\"./speciesindex.php\">birds</a>";
	navTrail(array_merge($birdItems, $extra));
}

function navTrailLocations($extra = "")
{
    $locationItems[] = "<a href=\"./locationindex.php\">locations</a>";
	navTrail(array_merge($locationItems, $extra));
}

function navTrailPhotos($extra = "")
{
	$photoItems[] = "photos";
	navTrail(array_merge($photoItems, $extra));
}

function navTrailTrips($extra = "")
{
	$tripItems[] = "<a href=\"./tripindex.php\">trips</a>";
	navTrail(array_merge($tripItems, $extra));
}

function navTrail($extra)
{ ?>
	<div class=navigationright><a href="./index.php">birdWalker</a>

<?	foreach ($extra as $item)
	{
		if (strlen($item) > 0)
		{ ?>
		  &gt; <?= $item ?>
<?		}
	} ?>

	</div>
<?
}

//
// -------------------------- DATABASE UTILITIES -------------------------------
//

function getEnableEdit()
{
	return true;
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

function getPhotoLinkForSightingInfo($sightingInfo, $fieldName="objectid")
{ ?>
	<a href="./photodetail.php?id=<?= $sightingInfo[$fieldName] ?>"><img border=0 align=center src="./images/camera.gif"></a>
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
		"<a href=\"./photodetail.php?id=" . $sightingInfo["objectid"] . "\">" .
		"<img " . $sizeAttributes . " src=\"./images/thumb/" . $thumbFilename . "\" border=0>" . 
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
function performQuery($queryString)
{
	$start = getmicrotime();
	selectDatabase();
	$theQuery = mysql_query($queryString) or die("<p>Error during query: " . $queryString . "</p><p>" . mysql_error() . "</p>");
	if (getEnableEdit())
	{ ?>

<!--  <?= round(getmicrotime() - $start, 3) ?> seconds -->
<!-- <?= $queryString ?> -->

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
	return $theCount[0];
}

/**
 * Select the birdwalker database, perform a one row query, die on error, return the first row.
 */
function performOneRowQuery($queryString)
{
	$theQuery = performQuery($queryString);
	$theFirstRow = mysql_fetch_array($theQuery);
	return $theFirstRow;
}

//
// -------------------------- SIGHTINGS, FIRST -------------------------------
//

function getSightingInfo($objectid)
{
	return performOneRowQuery("SELECT * FROM sighting where objectid=" . $objectid);
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
	return performOneRowQuery("SELECT * FROM species where objectid=" . $objectid);
}

function navTrailSpecies($speciesID)
{
	$orderInfo = getOrderInfo($speciesID);
	$familyInfo = getFamilyInfo($speciesID);

	$items[] = "<a href=\"./orderdetail.php?order=" . $orderInfo["objectid"] / pow(10, 9) . "\">" . strtolower($orderInfo["LatinName"]) . "</a>";
	$items[] = "<a href=\"./familydetail.php?family=" . $familyInfo["objectid"] / pow(10, 7) . "\">" . strtolower($familyInfo["LatinName"]) . "</a>";
	//	$items[] = strtolower($speciesInfo["CommonName"]);
	navTrailBirds($items);
}


function speciesBrowseButtons($url, $speciesID, $viewMode)
{
	$firstAndLastSpecies = performOneRowQuery("
    SELECT min(species.objectid) as firstOne, max(species.objectid) as lastOne
      FROM sighting, species
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation");

	$firstSpeciesID = $firstAndLastSpecies["firstOne"];
	$lastSpeciesID = $firstAndLastSpecies["lastOne"];

	$nextSpeciesID = performCount("
    SELECT min(species.objectid)
      FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid>" . $speciesID . " LIMIT 1");

	$prevSpeciesID = performCount("
    SELECT max(species.objectid)
      FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid<" . $speciesID . " LIMIT 1");

	browseButtons($url . "?view=" . $viewMode . "&speciesid=", $speciesID,
				  $firstSpeciesID, $prevSpeciesID, $nextSpeciesID, $lastSpeciesID);
}

/**
 * Displays a list of species common names that result from a search over
 * species and sighting tables.
 */
function formatTwoColumnSpeciesList($speciesQuery, $firstSightings = "", $firstYearSightings = "")
{
	// todo what about $speciesQuery->performQuery()

	$dbQuery = performQuery(
			$speciesQuery->getSelectClause() . " " .
			$speciesQuery->getFromClause() . " " .
			$speciesQuery->getWhereClause() . " " .
			$speciesQuery->getGroupByClause() . " ORDER BY species.objectid");

	if ($firstSightings == "") $firstSightings = getFirstSightings();

	$speciesCount = mysql_num_rows($dbQuery);
	$divideByTaxo = ($speciesCount > 30);
	$counter = round($speciesCount  * 0.52); ?>

	<table columns=2 width="100%" class=report-content>
      <tr valign=top><td width="50%">

<?
	while($info = mysql_fetch_array($dbQuery))
	{
		$orderNum =  floor($info["objectid"] / pow(10, 9));
		$temp = $info["earliestsighting"];
		$earliestsightingid = round(substr($temp, 10));
		
		if ($divideByTaxo && (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"])))
		{
			$taxoInfo = getBestTaxonomyInfo($info["objectid"]); ?>
			<div class=subheading><?= strtolower($taxoInfo["LatinName"]) ?></div>
<?		} ?>

		<div><a href="./speciesdetail.php?speciesid=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a>

<?      if ($info["sightingid"] != "") editLink("./sightingedit.php?id=" . $info["sightingid"]); ?>
<?      if ($info["Photo"] == "1") { ?><?= getPhotoLinkForSightingInfo($info, "sightingid") ?><? } ?>
<?		if ($info["ABACountable"] == "0") { ?>NOT ABA COUNTABLE<? } ?>
<?		if ($info["Exclude"] == "1") { ?>excluded<? } ?>

<? 		if ($firstSightings[$info["sightingid"]] != null) { ?> life bird <? }
		else if ($firstSightings[$earliestsightingid] != null) { ?> life bird <? }
		else if ($firstYearSightings[$sightingid] != null) { ?> year bird <? }
		if (strlen($info["Notes"]) > 0) { ?><div class=sighting-notes><?= $info["Notes"] ?></div><? } ?>

		</div>

<?		$prevInfo = $info;
		$counter--;
		if ($counter == 0)
		{ ?>
			</td><td width="50%">
<?		}
	} ?>

	</td></tr></table>
<?
}

function formatSpeciesListWithPhoto($speciesQuery)
{
	?><table columns=2><?

	$dbQuery = $speciesQuery->performQuery();

	while($info = mysql_fetch_array($dbQuery))
	{
		$photoQuery = performQuery("select * from sighting where SpeciesAbbreviation='" . $info["Abbreviation"] . "' and Photo='1' order by TripDate desc");

		?><tr><?

		?><td class=report-content align=right> <?
			   
		if ($photoInfo = mysql_fetch_array($photoQuery))
		{
			echo getThumbForSightingInfo($photoInfo);
		}

		?> <br/><br/></td>

		<td class=report-content valign=top>
            <a href="./speciesdetail.php?speciesid=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a><br>
            <i><?= $info["LatinName"] ?></i><br><br>
        </td><?

		?></tr><?

		$leftFlag = (! $leftFlag);
	}

	?></table><?
}

/**
 * Show a set of sightings, species by rows, years by columns.
 */
function formatSpeciesByYearTable($sightingQuery, $extraSightingListParams, $yearTotals)
{
    $gridQueryString="
    SELECT DISTINCT(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) AS mask " .
      $sightingQuery->getFromClause() . " " .
      $sightingQuery->getWhereClause() . " 
      GROUP BY sighting.SpeciesAbbreviation
      ORDER BY speciesid";

	$gridQuery = performQuery($gridQueryString); ?>

	<table columns=11 cellpadding=0 cellspacing=0 class="report-content" width="100%">
	    <tr><td></td><? insertYearLabels() ?></tr>
        <tr><td class=bordered>TOTAL</td>

<?	$info = mysql_fetch_array($yearTotals);
	for ($index = 1; $index <= 9; $index++)
	{
		if ($info["year"] == 1995 + $index)
		{ ?>
			<td class=bordered align=center>
                <a href="./specieslist.php?year=<?= 1995 + $index ?><?= $extraSightingListParams ?>"><?= $info["count"] ?></a>
            </td>
<?			$info = mysql_fetch_array($yearTotals);
		}
		else
		{ ?>
			<td class=bordered align=center>&nbsp;</td>
<?		}
	} ?>

        </tr>

<?	while ($info = mysql_fetch_array($gridQuery))
	{
		$theMask = $info["mask"];

		if (getBestTaxonomyID($prevInfo["speciesid"]) != getBestTaxonomyID($info["speciesid"]))
		{
			$taxoInfo = getBestTaxonomyInfo($info["speciesid"]); ?>
			<tr><td class=subheading colspan=11><?= strtolower($taxoInfo["LatinName"]) ?></td></tr>
<?		} ?>

		<tr><td><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

<?		for ($index = 1; $index <= 9; $index++)
		{ ?>
			<td class=bordered align=center>

<?			if (($info["mask"] >> $index) & 1)
			{ ?>				
				<a href="./sightinglist.php?speciesid=<?= $info["speciesid"] ?>&year=<?= (1995 + $index) . $extraSightingListParams?>">X</a>
<?			}
			else
			{ ?>
				&nbsp;
<?			} ?>
			 </td>
<?		} ?>

		</tr>

<?		$prevInfo = $info;
		$reprintYears++;
	} ?>

	</table>
<?
}


/**
 * Show a set of sightings, species by rows, months by columns.
 */
function formatSpeciesByMonthTable($sightingQuery, $extraSightingListParams, $monthTotals)
{
    $gridQueryString="
    SELECT DISTINCT(CommonName), species.objectid AS speciesid, bit_or(1 << month(TripDate)) AS mask " . 
      $sightingQuery->getFromClause() . " " .
      $sightingQuery->getWhereClause() . " 
      GROUP BY sighting.SpeciesAbbreviation
      ORDER BY speciesid";

	$gridQuery = performQuery($gridQueryString); ?>

	<table columns=11 cellpadding=0 cellspacing=0 class="report-content" width="100%">
	    <tr><td></td><? insertMonthLabels() ?></tr>
        <tr><td class=bordered>TOTAL</td>

<?	$info = mysql_fetch_array($monthTotals);
	for ($index = 1; $index <= 12; $index++)
	{
		if ($info["month"] == $index)
		{ ?>
			<td class=bordered align=center>
                <a href="./specieslist.php?month=<?= $index ?><?= $extraSightingListParams ?>"><?= $info["count"] ?></a>
            </td>
<?			$info = mysql_fetch_array($monthTotals);
		}
		else
		{ ?>
			<td class=bordered align=center>&nbsp;</td>
<?		}
	} ?>

        </tr>

<?	while ($info = mysql_fetch_array($gridQuery))
	{
		$theMask = $info["mask"];

		if (getBestTaxonomyID($prevInfo["speciesid"]) != getBestTaxonomyID($info["speciesid"]))
		{
			$taxoInfo = getBestTaxonomyInfo($info["speciesid"]); ?>
			<tr><td class=subheading colspan=13><?= strtolower($taxoInfo["LatinName"]) ?></td></tr>
<?		} ?>

		<tr><td width="40%"><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

<?		for ($index = 1; $index <= 12; $index++)
		{ ?>
			<td class=bordered align=center>

<?			if (($info["mask"] >> $index) & 1)
			{ ?>				
				<a href="./sightinglist.php?speciesid=<?= $info["speciesid"] ?>&month=<?= $index . $extraSightingListParams?>">X</a>
<?			}
			else
			{ ?>
				&nbsp;
<?			} ?>
			 </td>
<?		} ?>

		</tr>

<?		$prevInfo = $info;
		$reprintMonths++;
	} ?>

	</table>
<?
}

/**
 * Show locations as rows, years as columns
 */
function formatLocationByYearTable($locationQuery, $urlPrefix, $countyHeadingsOK = true)
{
	$lastStateHeading="";
    $gridQueryString="
      SELECT distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask " . 
      $locationQuery->getFromClause() . " " .
      $locationQuery->getWhereClause() . " 
        GROUP BY sighting.LocationName
        ORDER BY location.State, location.County, location.Name;";
	$gridQuery = performQuery($gridQueryString); ?>

    <table cellpadding=0 cellspacing=0 cols=11 class="report-content" width="100%">
    <tr><td></td><? insertYearLabels() ?></tr>

<?	while ($info = mysql_fetch_array($gridQuery))
	{
		$theMask = $info["mask"];

		if ($countyHeadingsOK && ($prevInfo["County"] != $info["County"])) { ?>
             <tr><td class=subheading colspan=11>
<?          if ($lastStateHeading != $info["State"]) { ?>
			    <b><?= getStateNameForAbbreviation($info["State"]) ?></b>,
<?              $lastStateHeading = $info["State"];
            } ?>
			<?= $info["County"] ?> County</td></tr>
<?		} ?>

		<tr>
		    <td width="40%">
		        <a href="./locationdetail.php?id=<?= $info["locationid"] ?>"><?= $info["LocationName"] ?></a>
            </td>

<?		for ($index = 1; $index <= 9; $index++)
		{ ?>
			<td class=bordered align=center>
<?			if (($theMask >> $index) & 1)
			{ ?>
				<a href="<?= $urlPrefix . $locationQuery->getParams() ?>&locationid= <?= $info["locationid"] ?>&year=<?= (1995 + $index) ?>">X</a>
<?			}
			else
			{ ?>
				&nbsp;
<?			} ?>
			</td>
<?		} ?>
		</tr>
<?
		$prevInfo = $info;
		$reprintYears++;
	} ?>

	 </table>
<?
} 


/**
 * Show locations as rows, months as columns
 */
function formatLocationByMonthTable($locationQuery, $urlPrefix, $countyHeadingsOK = true)
{
	$lastStateHeading="";

    $gridQueryString="
      SELECT distinct(LocationName), County, State, location.objectid AS locationid, bit_or(1 << month(TripDate)) AS mask " .
      $locationQuery->getFromClause() . " " .
      $locationQuery->getWhereClause() . " 
        GROUP BY sighting.LocationName
        ORDER BY location.State, location.County, location.Name;";

	$gridQuery = performQuery($gridQueryString); ?>

    <table cellpadding=0 cellspacing=0 cols=11 class="report-content" width="100%">
    <tr><td></td><? insertMonthLabels() ?></tr>

<?	while ($info = mysql_fetch_array($gridQuery))
	{
		$theMask = $info["mask"];

		if ($countyHeadingsOK && ($prevInfo["County"] != $info["County"])) { ?>
             <tr><td class=subheading colspan=13>
<?          if ($lastStateHeading != $info["State"]) { ?>
			    <b><?= getStateNameForAbbreviation($info["State"]) ?></b>,
<?              $lastStateHeading = $info["State"];
            } ?>
			<?= $info["County"] ?> County</td></tr>
<?		} ?>

		<tr>
		    <td>
		        <a href="./locationdetail.php?id=<?= $info["locationid"] ?>"><?= $info["LocationName"] ?></a>
            </td>

<?		for ($index = 1; $index <= 12; $index++)
		{ ?>
			<td class=bordered align=center>
<?			if (($theMask >> $index) & 1)
			{ ?>
				<a href="<?= $urlPrefix . $locationQuery->getParams() ?>&locationid= <?= $info["locationid"] ?>&month=<?= $index ?>">X</a>
<?			}
			else
			{ ?>
				&nbsp;
<?			} ?>
			</td>
<?		} ?>
		</tr>
<?
		$prevInfo = $info;
		$reprintMonths++;
	} ?>

	 </table>
<?
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

function getTripInfo($objectid)
{
	return performOneRowQuery("SELECT *, date_format(Date, '%W,  %M %e, %Y') as niceDate FROM trip where objectid=" . $objectid);
}

function getTripInfoForDate($inDate)
{
	return performOneRowQuery("SELECT *, date_format(Date, '%W,  %M %e, %Y') as niceDate FROM trip where Date='" . $inDate . "'");
}

function tripBrowseButtons($url, $tripID, $viewMode)
{
	$tripInfo = getTripInfo($tripID);

	$firstTripID = performCount("
    SELECT objectid FROM trip ORDER BY Date LIMIT 1");
	$lastTripID = performCount("
    SELECT objectid FROM trip ORDER BY Date DESC LIMIT 1");

	$nextTripID = performCount("
    SELECT objectid FROM trip
      WHERE Date > '" . $tripInfo["Date"] . "'
      ORDER BY Date LIMIT 1");
	$prevTripID = performCount("
    SELECT objectid FROM trip
      WHERE Date < '" . $tripInfo["Date"] . "'
      ORDER BY Date DESC LIMIT 1");

	if ($nextTripID == "") { $nextTripID = $sightingID; }
	if ($prevTripID == "") { $prevTripID = $sightingID; }

	browseButtons($url . "?view=" . $viewMode . "&tripid=", $tripID,
				  $firstTripID, $prevTripID, $nextTripID, $lastTripID);
}

function formatTwoColumnTripList($tripQuery)
{
	$tripCount = $tripQuery->getTripCount();
    $subdivideByYears = $tripCount > 20;
	$prevYear = "";
	$counter = round($tripCount  * 0.52); ?>
	
   <table class=report-content columns="2" width="100%">
      <tr valign=top><td>

<?	$dbQuery = $tripQuery->performQuery();
	while($info = mysql_fetch_array($dbQuery))
	{
		$thisYear =  substr($info["Date"], 0, 4);
		
		if (strcmp($thisYear, $prevYear) && $subdivideByYears)
		{ ?>
			<div class="subheading"><a name="<?= $thisYear ?>"></a><?= $thisYear ?></div>
<?		} ?>

			 <div>
                <a href="./tripdetail.php?tripid=<?= $info["objectid"] ?>">
				  <?= $info["Name"] ?>, <?= $info["niceDate"] ?><? if (! $subdivideByYears) echo ", " . $info["year"] ?>
                </a>
                <? if ($info["Photo"] == "1") { ?><?= getPhotoLinkForSightingInfo($info, "sightingid") ?><? } ?>
                <? if ($info["Exclude"] == "1") { ?>excluded<? } ?>
             </div>
             <? if ($info["sightingNotes"] != "") { ?> <div class=sighting-notes><?= $info["sightingNotes"] ?></div> <? } ?>

<?		$prevYear = $thisYear;
		$counter--;
		if ($counter == 0)
		{ ?>
		</td><td width="50%">
<?		}
	} ?>
      </td></tr>
	</table> <?
}

//
// ---------------------- LOCATIONS ------------------------
//

function getLocationInfo($objectid)
{
	return performOneRowQuery("SELECT * FROM location where objectid=" . $objectid);
}

function getLocationInfoForName($inLocationName)
{
	return performOneRowQuery("SELECT * FROM location WHERE Name='" . $inLocationName . "'");
}

function locationBrowseButtons($url, $locationID, $viewMode)
{
	$siteInfo = getLocationInfo($locationID);

	$firstLocationID = performCount("
      SELECT objectid FROM location ORDER BY CONCAT(State,County,Name) LIMIT 1");

	$lastLocationID = performCount("
      SELECT objectid FROM location ORDER BY CONCAT(State,County,Name) DESC LIMIT 1");

	$nextLocationID = performCount("
      SELECT objectid FROM location
        WHERE CONCAT(State,County,Name) > '" . $siteInfo["State"] . $siteInfo["County"] . $siteInfo["Name"] . "'
        ORDER BY CONCAT(State,County,Name) LIMIT 1");

	$prevLocationID = performCount("
      SELECT objectid FROM location
        WHERE CONCAT(State,County,Name) < '" . $siteInfo["State"] . $siteInfo["County"] . $siteInfo["Name"] . "'
        ORDER BY CONCAT(State,County,Name) DESC LIMIT 1");

	browseButtons($url . "?view=" . $viewMode . "&id=", $locationID,
				  $firstLocationID, $prevLocationID, $nextLocationID, $lastLocationID);
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
	$firstAndLastState= performOneRowQuery("
    SELECT min(state.objectid) as firstOne, max(state.objectid) as lastOne
      FROM state, sighting, location
      WHERE sighting.LocationName=location.Name AND location.State=state.Abbreviation");

	$firstStateID = $firstAndLastState["firstOne"];
	$lastStateID = $firstAndLastState["lastOne"];

	$nextStateID = performCount("
    SELECT min(state.objectid)
      FROM state, sighting, location
      WHERE sighting.LocationName=location.Name AND location.State=state.Abbreviation and state.objectid>" . $stateID . " LIMIT 1");

	$prevStateID = performCount("
    SELECT max(state.objectid)
      FROM state, sighting, location
      WHERE sighting.LocationName=location.Name AND location.State=state.Abbreviation and state.objectid<" . $stateID . " LIMIT 1");

	browseButtons("./statedetail.php?view=" . $viewMode . "&stateid=", $stateID,
				  $firstStateID, $prevStateID, $nextStateID, $lastStateID);

}

function navTrailLocationDetail($siteInfo)
{
	$stateInfo = getStateInfoForAbbreviation($siteInfo["State"]);

	$items[] = "
    <a href=\"./statedetail.php?view=locations&id=" .  $stateInfo["objectid"] . "\">" .
		strtolower(getStateNameForAbbreviation($siteInfo["State"])) . "
    </a>";
	$items[] = "
    <a href=\"./countydetail.php?view=locations&county=" . $siteInfo["County"] . "&state=" . $siteInfo["State"] . "\">" .
		 strtolower($siteInfo["County"]) . " county
    </a>";

	navTrailLocations($items);
}

function rightThumbnailSpecies($abbrev)
{
	rightThumbnail(
    "SELECT sighting.*, rand() AS shuffle
      FROM sighting
      WHERE sighting.Photo='1' AND sighting.SpeciesAbbreviation='" . $abbrev . "'
      ORDER BY shuffle
      LIMIT 1",
	  true);
}

function rightThumbnailCounty($countyName)
{
	rightThumbnail(
    "SELECT sighting.*, rand() AS shuffle
      FROM sighting, location
      WHERE sighting.Photo='1' AND sighting.LocationName=location.Name AND location.County='" . $countyName . "'
      ORDER BY shuffle
      LIMIT 1",
      true);
}

function rightThumbnailState($stateCode)
{
	rightThumbnail(
      "SELECT sighting.*, rand() AS shuffle
        FROM sighting, location
        WHERE sighting.Photo='1' AND sighting.LocationName=location.Name AND location.State='" . $stateCode . "'
        ORDER BY shuffle LIMIT 1",
        true);
}

function rightThumbnailLocation($locationName)
{
  rightThumbnail("
    SELECT *, rand() AS shuffle
      FROM sighting
      WHERE Photo='1' AND LocationName='" . $locationName . "'
      ORDER BY shuffle LIMIT 1",
      true);
}

function mapLink($siteInfo)
{
   if ($siteInfo["Latitude"] > 0)
   {
	   $lat = $siteInfo["Latitude"];
	   $long = $siteInfo["Longitude"];
?>
   <div>maps: 
      <a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude=<?= $lat ?>&longitude=-<?= $long ?>">mapquest</a> |
      <a href="http://terraserver.microsoft.com/image.aspx?Lon=-<?=$long?>&Lat=<?=$lat?>&w=1">terraserver</a> |
      <a href="./locationmap.php?minlat=<?=$lat-0.1?>&maxlat=<?=$lat+0.1?>&minlong=<?=$long-0.1?>&maxlong=<?=$long+0.1?>">opengis</a>
    </div>
<? }
}

function formatTwoColumnLocationList($locationQuery, $countyHeadingsOK = true)
{
	$dbQuery = performQuery(
			$locationQuery->getSelectClause() . " " .
			$locationQuery->getFromClause() . " " .
			$locationQuery->getWhereClause() . " ORDER BY location.State, location.County, location.Name");

	$lastStateHeading="";
	$prevInfo=null;
	$locationCount = mysql_num_rows($dbQuery);
	$divideByCounties = ($locationCount > 20);
	$counter = round($locationCount  * 0.5); ?>

    <table class=report-content width="100%">
      <tr valign=top><td width="50%">

<?	while($info = mysql_fetch_array($dbQuery))
	{
		if ($countyHeadingsOK && $divideByCounties && (($prevInfo["State"] != $info["State"]) || ($prevInfo["County"] != $info["County"])))
		{ ?>
			<div class="subheading">
<?          if ($lastStateHeading != $info["State"]) { ?>
			    <b><?= getStateNameForAbbreviation($info["State"]) ?></b>,
<?              $lastStateHeading = $info["State"];
            } ?>
			    <?= $info["County"] ?> County
            </div>
<?		} ?>

		<div><a href="./locationdetail.php?id=<?= $info["objectid"] ?>"><?= $info["Name"] ?></a></div>

<?		$prevInfo = $info;   
		$counter--;
		if ($counter == 0)
		{ ?>
		</td><td width="50%">
<?		}
	} ?>

	</tr></table>
<?
}


// -------------------------------------- TIME -----------------------------------
//

function insertYearLabels()
{
	for ($year = 1996; $year <= getLatestYear(); $year++)
	{ ?>
		<td class=yearcell align=center><?= $year ?></td>
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


?>
