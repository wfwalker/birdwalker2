
<?php

require("./birdwalker.php");

$speciesid = $_GET["speciesid"];
$locationid = $_GET["locationid"];
$year = $_GET["year"];
$speciesInfo = getSpeciesInfo($speciesid);

$sig2spe = "species.Abbreviation=sighting.SpeciesAbbreviation";
$sig2loc = "location.Name=sighting.LocationName";
$sig2tri = "trip.Date=sighting.TripDate";

$sightingListQueryString = "SELECT date_format(sighting.TripDate, '%M %e, %Y') as niceDate, sighting.*, species.CommonName, species.objectid as speciesid, trip.objectid as tripid, location.County, location.State FROM sighting, species, location, trip WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND location.Name=sighting.LocationName AND species.objectid=" . $speciesid . " AND Year(TripDate)=" . $year . " AND trip.Date=sighting.TripDate ";
if ($locationid != "") { $sightingListQueryString = $sightingListQueryString . "AND location.objectid=" . $locationid; }
$sightingListQueryString = $sightingListQueryString . " order by TripDate, LocationName;";

$sightingListQuery = performQuery($sightingListQueryString);
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?> Sightings for <?php echo $year ?></title>
  </head>
  <body>

<?php navigationHeader() ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle><?php echo $speciesInfo["CommonName"] ?> Sightings for <?php echo $year ?></div>
      </div>

<p class=sighting-notes>
Note: within a single day, the order of sightings is not
preserved.
</p>

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
	if ($sightingInfo["Notes"] != "") { echo "<tr><td></td><td></td><td colspan=2 class=sighting-notes>" . $sightingInfo["Notes"] . "</td></tr>"; }

	$counter++;
	$prevSightingInfo = $sightingInfo;
}


?>
    </div>
  </body>
</html>