
<?php

require("./birdwalker.php");

$speciesid = $_GET["speciesid"];
$locationid = $_GET["locationid"];
$year = $_GET["year"];
$month = $_GET["month"];
$county = $_GET["county"];
$state = $_GET["state"];

$sightingListQueryString = "SELECT date_format(sighting.TripDate, '%M %e, %Y') as niceDate, sighting.*, species.CommonName, species.objectid as speciesid, trip.objectid as tripid, location.County, location.State FROM sighting, species, location, trip WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND location.Name=sighting.LocationName AND trip.Date=sighting.TripDate ";

if ($speciesid !="") {
	$sightingListQueryString = $sightingListQueryString . " AND species.objectid=" . $speciesid;
	$speciesInfo = getSpeciesInfo($speciesid);
	$pageTitle = $speciesInfo["CommonName"];
} else {
	$pageTitle = "Sightings";
}

if ($locationid != "") {
	$sightingListQueryString = $sightingListQueryString . " AND location.objectid=" . $locationid;
	$locationInfo = getLocationInfo($locationid); 
	$pageSubtitle = $locationInfo["Name"];
} elseif ($county != "") {
	$sightingListQueryString = $sightingListQueryString . " AND location.County='" . $county . "'";
	$pageSubtitle = $county . " County";
} elseif ($state != "") {
	$sightingListQueryString = $sightingListQueryString . " AND location.State='" . $state . "'";
	  $pageSubtitle = getStateNameForAbbreviation($state);
}

if ($month !="") {
	$sightingListQueryString = $sightingListQueryString . " AND Month(TripDate)=" . $month;
	if ($pageSubtitle == "" ) {
		$pageTitle = $pageTitle . ", " . getMonthNameForNumber($month);
	} else {
		$pageSubtitle = $pageSubtitle . ", " . getMonthNameForNumber($month);
	}
}
if ($year !="") {
	$sightingListQueryString = $sightingListQueryString . " AND Year(TripDate)=" . $year;
	if ($pageSubtitle == "" ) {
		$pageTitle = $pageTitle . ", " . $year;
	} else {
		$pageSubtitle = $pageSubtitle . ", " . $year;
	}
}

$sightingListQueryString = $sightingListQueryString . " order by TripDate, LocationName;";

$sightingListQuery = performQuery($sightingListQueryString);
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $pageTitle ?></title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle><?= $pageTitle ?></div>
	  <div class=pagesubtitle><?= $pageSubtitle ?></div>
      <div class=metadata><?= mysql_num_rows($sightingListQuery) ?> Sightings</div>
      </div>

<table class=report-content columns=4 width="600px">
<?php
while($sightingInfo = mysql_fetch_array($sightingListQuery)) {
?>
    <tr>
    <td nowrap>
<?
	if ($prevSightingInfo['TripDate'] != $sightingInfo['TripDate']) {
?>
        <a href="./tripdetail.php?id=<?= $sightingInfo['tripid'] ?>"><?= $sightingInfo['niceDate'] ?></a>
<?
	}
	if (getEnableEdit()) {
?>
        <a href="./sightingedit.php?id=<?= $sightingInfo['objectid'] ?>">edit</a>
<?
    }
	if ($sightingInfo["Photo"] == "1") {
?>
        <?= getPhotoLinkForSightingInfo($sightingInfo) ?>
<?
    }
?>
    </td>
    <td>
<?
	if ($prevSightingInfo['LocationName'] != $sightingInfo['LocationName']) {
?>
		<?= $sightingInfo['LocationName'] ?>, <?= $sightingInfo['County'] ?> County, <?= $sightingInfo['State'] ?>
<?
	}
?>
	</td>
    </tr>
<?	
	if ($sightingInfo["Notes"] != "") {
?>
    <tr><td></td><td class=sighting-notes><?= $sightingInfo["Notes"] ?></td></tr>
<?
    }

	$counter++;
	$prevSightingInfo = $sightingInfo;
}
?>

    </div>
  </body>
</html>