<?php

function globalMenu()
{ ?>
	<div class="contentleft">
	  <div class="leftsubtitle"><a href="./tripindex.php">trips</a></div>
	  <div class="leftsubtitle"><a href="./speciesindex.php">birds</a></div>
	  <div class="leftsubtitle"><a href="./locationindex.php">locations</a></div>
	  <div class="leftsubtitle"><a href="./chronolifelist.php">life list</a></div>
	  <div class="leftsubtitle"><a href="./photoindextaxo.php">photos</a></div>

<?	if (getEnableEdit())
	{ ?>
		<br><div class="leftsubtitle">
		<a href="./tripcreate.php">create trip</a><br>
		<a href="./photosneeded.php">photos needed</a><br>
		<a href="./errorcheck.php">errors</a>
		</div>
<?	} ?>

    </div>
<?
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
	$prevLabel="prev";

?>
   <div class="navigationleft">
<?	
	if ($current == $first)
	{
?>
		<span class=navbutton><?= $firstLabel ?></span> <span class=navbutton><?= $prevLabel ?></span>
<?
	}
	else
	{
?>
		<span class=navbutton><a href="<?= $urlPrefix . $first ?>"><?= $firstLabel ?></a></span>
		<span class=navbutton><a href="<?= $urlPrefix . $prev ?>"><?= $prevLabel ?></a></span>
<?
	}

	if ($current == $last)
	{
?>
		<span class=navbutton><?= $nextLabel ?></span> <span class=navbutton><?= $lastLabel ?></span>
<?
	}
	else
	{
?>
		<span class=navbutton><a href="<?= $urlPrefix . $next ?>"><?= $nextLabel ?></a></span>
		<span class=navbutton><a href="<?= $urlPrefix . $last ?>"><?= $lastLabel ?></a></span>
<?
	}
?>
	</div>
<?
}

function pageThumbnail($photoQueryString)
{
	$photoQuery = performQuery($photoQueryString);

	if (mysql_num_rows($photoQuery) > 0)
	{
		$photoInfo = mysql_fetch_array($photoQuery);
?>
		<div class=thumb><?= getThumbForSightingInfo($photoInfo) ?><div class=copyright>@2004 W. F. Walker</div></div>
<?
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
{ ?>
	<a href="./photodetail.php?id=<?= $sightingInfo["objectid"] ?>"><img border=0 align=center src="./images/camera.gif"></a>
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
		<!--  <?= (1000 * (microtime(1) - $start)) ?>, <?= $queryString ?> -->
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
?>
		<td class=yearcell align=center><a href="./yeardetail.php?year=<?= $year ?>"><?= $year ?></a></td>
<?
	}
}

function formatTwoColumnSpeciesList($query)
{
	$speciesCount = mysql_num_rows($query);
	$divideByTaxo = ($speciesCount > 30);
	$counter = round($speciesCount  * 0.6); ?>

	<div class=col1>

<?	while($info = mysql_fetch_array($query))
	{
		$orderNum =  floor($info["objectid"] / pow(10, 9));
		
		if ($divideByTaxo && (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"])))
		{
			$taxoInfo = getBestTaxonomyInfo($info["objectid"]); ?>
			<div class=heading><?= strtolower($taxoInfo["LatinName"]) ?></div>
<?		} ?>

		<div class=firstcell><a href="./speciesdetail.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a>

<?		if ($info["ABACountable"] == "0")
		{ ?>
			NOT ABA COUNTABLE
<?		} ?>
		</div>

<?		$prevInfo = $info;
		$counter--;
		if ($counter == 0)
		{ ?>
			</div><div class=col2>
<?		}
	} ?>

	</div>
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
			<td class=bordered align=center><?= $info["count"] ?></td>
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
			<tr><td class=heading colspan=11><?= strtolower($taxoInfo["LatinName"]) ?></td></tr>
<?		} ?>

		<tr><td class=firstcell><a href="./speciesdetail.php?id=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>

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
             <tr><td class=heading colspan=11>
<?          if ($lastStateHeading != $info["State"]) { ?>
			    <b><?= getStateNameForAbbreviation($info["State"]) ?></b>,
<?              $lastStateHeading = $info["State"];
            } else { ?>
				&nbsp;
<?          } ?>
			<?= $info["County"] ?> County</td></tr>
<?		} ?>

		<tr>
		    <td class=firstcell>
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

//
// ---------------------- LOCATIONS ------------------------
//

function getLocationInfo($objectid)
{
	return performOneRowQuery("SELECT * FROM location where objectid=" . $objectid);
}

function formatTwoColumnLocationList($locationListQuery, $countyHeadingsOK = true)
{
	$lastStateHeading="";
	$prevInfo=null;
	$locationCount = mysql_num_rows($locationListQuery);
	$divideByCounties = ($locationCount > 20);
	$counter = round($locationCount  * 0.6); ?>

    <div class=col1>

<?	while($info = mysql_fetch_array($locationListQuery))
	{
		if ($countyHeadingsOK && $divideByCounties && (($prevInfo["State"] != $info["State"]) || ($prevInfo["County"] != $info["County"])))
		{ ?>
			<div class="heading">
<?          if ($lastStateHeading != $info["State"]) { ?>
			    <b><?= getStateNameForAbbreviation($info["State"]) ?></b>,
<?              $lastStateHeading = $info["State"];
            } else { ?>
				&nbsp;
<?          } ?>
			    <?= $info["County"] ?> County</td></tr>
            </div>
<?		} ?>

		<div class=firstcell><a href="./locationdetail.php?id=<?= $info["objectid"] ?>"><?= $info["Name"] ?></a></div>

<?		$prevInfo = $info;   
		$counter--;
		if ($counter == 0)
		{ ?>
		</div><div class=col2>
<?		}
	} ?>

	</div>
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
