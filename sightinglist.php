
<?php

require("./birdwalker.php");

$speciesid = $_GET["speciesid"];
$locationid = $_GET["locationid"];
$year = $_GET["year"];
$county = $_GET["county"];
$state = $_GET["state"];

$sightingListQueryString = "SELECT date_format(sighting.TripDate, '%M %e, %Y') as niceDate, sighting.*, species.CommonName, species.objectid as speciesid, trip.objectid as tripid, location.County, location.State FROM sighting, species, location, trip WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND location.Name=sighting.LocationName AND trip.Date=sighting.TripDate ";

if ($speciesid !="") {
	$sightingListQueryString = $sightingListQueryString . " AND species.objectid=" . $speciesid;
	$speciesInfo = getSpeciesInfo($speciesid);
	$pageTitle = $speciesInfo["CommonName"] . " sightings";
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
    <title>birdWalker | <?php echo $pageTitle ?></title>
  </head>
  <body>

<?php navigationHeader() ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle><?php echo $pageTitle ?></div>
	  <div class=pagesubtitle><?php echo $pageSubtitle ?></div>
      </div>

<table class=report-content columns=4 width="600px">
<?php
while($sightingInfo = mysql_fetch_array($sightingListQuery)) {
	echo "<tr>";
	// date
	echo "<td nowrap>";
	if ($prevSightingInfo['TripDate'] != $sightingInfo['TripDate']) {
		echo "<a href=\"./tripdetail.php?id=" . $sightingInfo['tripid'] . "\">" . $sightingInfo['niceDate'] . "</a>";
	}
		// edit link
	if (getEnableEdit()) { echo " <a href=\"./sightingedit.php?id=" . $sightingInfo['objectid'] . "\">edit</a>"; }
	echo" </td>";

	// location
	echo "<td>";
	if ($prevSightingInfo['LocationName'] != $sightingInfo['LocationName']) {
		echo $sightingInfo['LocationName'] . ", " . $sightingInfo['County'] . " County, " . $sightingInfo['State'];
	}
	echo "</td>";

	echo "</tr>";
	
	// notes
	if ($sightingInfo["Notes"] != "") { echo "<tr><td></td><td class=sighting-notes>" . $sightingInfo["Notes"] . "</td></tr>"; }

	$counter++;
	$prevSightingInfo = $sightingInfo;
}


?>
    </div>
  </body>
</html>