<?php

function globalMenu()
{ ?>
	<div class="contentleft">
      <p><img src="./images/bill.jpg"></p>
	  <div class="leftsubtitle"><a href="./tripindex.php">trips</a></div>
	  <div class="leftsubtitle"><a href="./speciesindex.php">birds</a></div>
	  <div class="leftsubtitle"><a href="./locationindex.php">locations</a></div>
	  <div class="leftsubtitle"><a href="./chronolifelist.php">life list</a></div>
	  <div class="leftsubtitle"><a href="./photoindextaxo.php">photos</a></div>
	  <div class="leftsubtitle"><a href="./credits.php">credits</a></div>

<?	if (getEnableEdit())
	{ ?>
		<br><div class="leftsubtitle">
		<a href="./tripcreate.php">create trip</a><br>
		<a href="./photosneeded.php">photos needed</a><br>
		<a href="./errorcheck.php">error check</a><br>
		</div>
<?	} ?>

    </div>
<?
}

function editLink($href)
{ 
    if (getEnableEdit()) { ?>
       <a href="<?= $href ?>"><img src="./images/edit.gif" border=0></a>
<?  }
}

function disabledBrowseButtons()
{
	browseButtons("", 0, 0, 0, 0, 0);
}


function browseButtons($urlPrefix, $current, $first, $prev, $next, $last)
{
	$firstLabel="first";
	$lastLabel="last";
	$nextLabel="next";
	$prevLabel="prev"; ?>

   <div class="navigationleft">

<?	if ($current == $first)
	{ ?>
		<span class=navbutton><?= $firstLabel ?></span> <span class=navbutton><?= $prevLabel ?></span>
<?	}
	else
	{ ?>
		<span class=navbutton><a href="<?= $urlPrefix . $first ?>"><?= $firstLabel ?></a></span>
		<span class=navbutton><a href="<?= $urlPrefix . $prev ?>"><?= $prevLabel ?></a></span>
<?	}

	if ($current == $last)
	{?>
		<span class=navbutton><?= $nextLabel ?></span> <span class=navbutton><?= $lastLabel ?></span>
<?	}
	else
	{ ?>
		<span class=navbutton><a href="<?= $urlPrefix . $next ?>"><?= $nextLabel ?></a></span>
		<span class=navbutton><a href="<?= $urlPrefix . $last ?>"><?= $lastLabel ?></a></span>
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

function rightThumbnail($photoQueryString)
{
	$photoQuery = performQuery($photoQueryString);

	if (mysql_num_rows($photoQuery) > 0)
	{
		$photoInfo = mysql_fetch_array($photoQuery);
		$filename = getPhotoFilename($photoInfo);
		list($width, $height, $type, $attr) = getimagesize("./images/thumb/" . $filename); ?>

        <a href="./photodetail.php?id=<?= $photoInfo["objectid"] ?>">
           <img width=<?= $width ?> height=<?= $height ?> src="./images/thumb/<?= $filename ?>" border=0 align="left" class="inlinepict">
        </a>
<?	}
}

function rightThumbnailAll()
{
	rightThumbnail("SELECT *, rand() AS shuffle FROM sighting WHERE Photo='1' ORDER BY shuffle LIMIT 1");
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

	return "<a href=\"./photodetail.php?id=" . $sightingInfo["objectid"] . "\"><img width=" . $width . " height=" . $height . " src=\"./images/thumb/" . $thumbFilename . "\" border=0></a>";
}

/**
 * Select the birdwalker database, perform a query, die on error, return the query.
 */
function performQuery($queryString)
{
	$start = microtime(1);
	selectDatabase();
	$theQuery = mysql_query($queryString) or die("<p>Error during query: " . $queryString . "</p><p>" . mysql_error() . "</p>");
	if (getEnableEdit())
	{ ?>
		<!--  <?= (1000 * (microtime(1) - $start)) ?>, <?= $queryString ?>, <?= mysql_num_rows($theQuery) ?> rows -->
<?	}
	return $theQuery;
}

/**
 * Select the birdwalker database, perform a counting query, die on error, return the count.
 */
function performCount($queryString)
{
	selectDatabase();
	if (getEnableEdit())
	{ ?>
		<!-- <?= $queryString ?> -->
<?	}
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
	if (getEnableEdit())
	{ ?>
		<!-- <?= $queryString  ?> -->
<?	}
	$theQuery = mysql_query($queryString) or die("single row query error " . $queryString);
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
 * Find the first sighting for each species.
 */
function getFirstSightings()
{
	$firstSightings = null;

	performQuery("CREATE TEMPORARY TABLE tmp ( abbrev varchar(16) default NULL, tripdate date default NULL);");

	performQuery("
      INSERT INTO tmp
        SELECT SpeciesAbbreviation, MIN(TripDate)
        FROM sighting, species
        WHERE Exclude!='1' AND sighting.SpeciesAbbreviation=species.Abbreviation AND species.ABACountable='1'
        GROUP BY SpeciesAbbreviation
        ORDER BY species.objectid;");

	$firstSightingQuery = performQuery("
      SELECT sighting.objectid, tmp.tripdate FROM sighting, tmp
        WHERE sighting.SpeciesAbbreviation=tmp.abbrev AND sighting.TripDate=tmp.tripdate
        ORDER BY tripdate;");

	$index = 1;
	while ($info = mysql_fetch_array($firstSightingQuery))
	{
		$firstSightingID = $info["objectid"];
		$firstSightings[$firstSightingID] = $index;
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
	$firstSightings = null;

	performQuery("CREATE TEMPORARY TABLE tmp ( abbrev varchar(16) default NULL, tripdate date default NULL);");
	performQuery("
      INSERT INTO tmp
      SELECT SpeciesAbbreviation, MIN(TripDate)
        FROM sighting, species
        WHERE Exclude!='1' AND year(TripDate)='" . $theYear . "'
          AND species.Abbreviation=sighting.SpeciesAbbreviation AND species.ABACountable='1'
        GROUP BY SpeciesAbbreviation
        ORDER BY species.objectid;");
	$firstSightingQuery = performQuery("
      SELECT sighting.objectid, tmp.tripdate
        FROM sighting, tmp
        WHERE sighting.SpeciesAbbreviation=tmp.abbrev
          AND sighting.TripDate=tmp.tripdate
      ORDER BY tripdate");

	$index = 1;
	while ($info = mysql_fetch_array($firstSightingQuery))
	{
		$firstSightingID = $info["objectid"];
		$firstSightings[$firstSightingID] = $index;
		$index++;
	}

	performQuery("DROP TABLE tmp;");

	return $firstSightings;
}


//
// ---------------------------- SPECIES ---------------------------
//

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

function speciesViewLinks($speciesID)
{
?>
      <a href="./speciesdetail.php?id=<?=$speciesID?>">list</a> |
      <a href="./speciesdetailbymonth.php?id=<?=$speciesID?>">by month</a> |
      <a href="./speciesdetailbyyear.php?id=<?=$speciesID?>">by year</a>
<?
}

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
	$items[] = strtolower($speciesInfo["CommonName"]);
	navTrailBirds($items);
}


function speciesBrowseButtons($speciesID, $viewMode)
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

	browseButtons("./speciesdetail" . $viewMode . ".php?id=", $speciesID, $firstSpeciesID, $prevSpeciesID, $nextSpeciesID, $lastSpeciesID);

}

/**
 * Displays a list of species common names that result from a search over
 * species and sighting tables.
 */
function formatTwoColumnSpeciesList($query, $firstSightings = "", $firstYearSightings = "")
{
	if ($firstSightings == "") $firstSightings = getFirstSightings();

	$speciesCount = mysql_num_rows($query);
	$divideByTaxo = ($speciesCount > 30);
	$counter = round($speciesCount  * 0.52); ?>

	<table columns=2 width="100%" class=report-content>
      <tr valign=top><td width="50%">

<?	while($info = mysql_fetch_array($query))
	{
		$orderNum =  floor($info["objectid"] / pow(10, 9));
		
		if ($divideByTaxo && (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"])))
		{
			$taxoInfo = getBestTaxonomyInfo($info["objectid"]); ?>
			<div class=subheading><?= strtolower($taxoInfo["LatinName"]) ?></div>
<?		} ?>

		<div><a href="./speciesdetail.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a>

<?      if ($info["sightingid"] != "") editLink("./sightingedit.php?id=" . $info["sightingid"]); ?>
<?      if ($info["Photo"] == "1") { ?><?= getPhotoLinkForSightingInfo($info, "sightingid") ?><? } ?>
<?		if ($info["ABACountable"] == "0") { ?>NOT ABA COUNTABLE<? } ?>
<?		if ($info["Exclude"] == "1") { ?>excluded<? } ?>
<? 		if ($firstSightings[$info["sightingid"]] != null) { ?> life bird #<?= $firstSightings[$info["sightingid"]] ?> <? }
		else if ($firstYearSightings[$info["sightingid"]] != null) { ?> year bird #<?= $firstYearSightings[$info["sightingid"]] ?> <? } ?>
<?		if (strlen($info["Notes"]) > 0) { ?><div class=sighting-notes><?= $info["Notes"] ?></div><? } ?>

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

/**
 * Show a set of sightings, species by rows, years by columns.
 */
function formatSpeciesByYearTable($gridQueryString, $extraSightingListParams, $yearTotals)
{
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

		<tr><td><a href="./speciesdetail.php?id=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

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
function formatSpeciesByMonthTable($gridQueryString, $extraSightingListParams, $monthTotals)
{
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

		<tr><td width="40%"><a href="./speciesdetail.php?id=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

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
function formatLocationByYearTable($gridQueryString, $urlPrefix, $countyHeadingsOK = true)
{
	$lastStateHeading="";
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
				<a href="<?= $urlPrefix ?>locationid= <?= $info["locationid"] ?>&year=<?= (1995 + $index) ?>">X</a>
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
function formatLocationByMonthTable($gridQueryString, $urlPrefix, $countyHeadingsOK = true)
{
	$lastStateHeading="";
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
				<a href="<?= $urlPrefix ?>locationid= <?= $info["locationid"] ?>&month=<?= $index ?>">X</a>
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

function formatTwoColumnTripList($tripListQuery)
{
	$tripCount = mysql_num_rows($tripListQuery);
    $subdivideByYears = $tripCount > 20;
	$prevYear = "";
	$counter = round($tripCount  * 0.52); ?>
	
   <table class=report-content columns="2" width="100%">
      <tr valign=top><td>

<?	while($info = mysql_fetch_array($tripListQuery))
	{
		$thisYear =  substr($info["Date"], 0, 4);
		
		if (strcmp($thisYear, $prevYear) && $subdivideByYears)
		{ ?>
			<div class="subheading"><a name="<?= $thisYear ?>"></a><?= $thisYear ?></div>
<?		} ?>

			 <div><a href="./tripdetail.php?id=<?= $info["objectid"] ?>"><?= $info["Name"] ?>, <?= $info["niceDate"] ?></a></div>
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

function locationBrowseButtons($siteInfo, $locationID, $viewMode)
{
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

	browseButtons("./locationdetail" . $viewMode . ".php?id=", $locationID, $firstLocationID, $prevLocationID, $nextLocationID, $lastLocationID);
}

function navTrailCounty($state, $county)
{
	$items[]="<a href=\"./statespecies.php?state=" . $state . "\">" . strtolower(getStateNameForAbbreviation($state)) . "</a>";
	$items[] = strtolower($county . " county");
	navTrailLocations($items);
}

function navTrailLocationDetail($siteInfo)
{
	$items[] = "
    <a href=\"./statelocations.php?state=" .  $siteInfo["State"] . "\">" .
		strtolower(getStateNameForAbbreviation($siteInfo["State"])) . "
    </a>";
	$items[] = "
    <a href=\"./countylocations.php?county=" . $siteInfo["County"] . "&state=" . $siteInfo["State"] . "\">" .
		 strtolower($siteInfo["County"]) . " county
    </a>";
	$items[] =
		 strtolower($siteInfo["Name"]);

	navTrailLocations($items);
}

function rightThumbnailSpecies($abbrev)
{
	rightThumbnail(
    "SELECT sighting.*, rand() AS shuffle
      FROM sighting
      WHERE sighting.Photo='1' AND sighting.SpeciesAbbreviation='" . $abbrev . "'
      ORDER BY shuffle
      LIMIT 1");
}

function rightThumbnailCounty($countyName)
{
	rightThumbnail(
    "SELECT sighting.*, rand() AS shuffle
      FROM sighting, location
      WHERE sighting.Photo='1' AND sighting.LocationName=location.Name AND location.County='" . $countyName . "'
      ORDER BY shuffle
      LIMIT 1");
}

function rightThumbnailState($stateCode)
{
	rightThumbnail(
      "SELECT sighting.*, rand() AS shuffle
        FROM sighting, location
        WHERE sighting.Photo='1' AND sighting.LocationName=location.Name AND location.State='" . $stateCode . "'
        ORDER BY shuffle LIMIT 1");
}

function rightThumbnailLocation($locationName)
{
  rightThumbnail("
    SELECT *, rand() AS shuffle
      FROM sighting
      WHERE Photo='1' AND LocationName='" . $locationName . "'
      ORDER BY shuffle LIMIT 1");
}

function mapLink($siteInfo)
{
   if (strlen($siteInfo["Latitude"]) > 0) { ?>
	<div>
      <a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude=<?= $siteInfo["Latitude"] ?>&longitude=-<?= $siteInfo["Longitude"] ?>">Map...</a>
    </div>
<? }
}

function locationViewLinks($locationID)
{
?>
      <a href="./locationdetail.php?id=<?=$locationID?>">list</a> |
      <a href="./locationdetailbymonth.php?id=<?=$locationID?>">by month</a> |
      <a href="./locationdetailbyyear.php?id=<?=$locationID?>">by year</a>
<?
}

function countyViewLinks($state, $county)
{
?>
        locations:
        <a href="./countylocations.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">list</a> |
	    <a href="./countylocationsbyyear.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">by year</a> |
	    <a href="./countylocationsbymonth.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">by month</a>
        species:	
        <a href="./countyspecies.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">list</a> |
	    <a href="./countyspeciesbyyear.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">by year</a> |
	    <a href="./countyspeciesbymonth.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">by month</a>
<?
}

function stateViewLinks($abbrev)
{
?>
        locations:
        <a href="./statelocations.php?state=<?= $abbrev ?>">list</a> |
	    <a href="./statelocationsbyyear.php?state=<?= $abbrev ?>">by year</a> |
	    <a href="./statelocationsbymonth.php?state=<?= $abbrev ?>">by month</a>
        species:	
        <a href="./statespecies.php?state=<?= $abbrev ?>">list</a> |
	    <a href="./statespeciesbyyear.php?state=<?= $abbrev ?>">by year</a> |
	    <a href="./statespeciesbymonth.php?state=<?= $abbrev ?>">by month</a>
<?
}


function formatTwoColumnLocationList($locationListQuery, $countyHeadingsOK = true)
{
	$lastStateHeading="";
	$prevInfo=null;
	$locationCount = mysql_num_rows($locationListQuery);
	$divideByCounties = ($locationCount > 20);
	$counter = round($locationCount  * 0.5); ?>

    <table class=report-content width="100%">
      <tr valign=top><td width="50%">

<?	while($info = mysql_fetch_array($locationListQuery))
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
	for ($year = 1996; $year <= 2004; $year++)
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
