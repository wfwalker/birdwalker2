<?php

require_once("./birdwalker.php");
require_once("./request.php");

$badAbbrevs = performQuery("Find Bad Abbreviations",
    "SELECT sighting.*, sighting.id AS sightingid
      FROM sighting
      WHERE species_id = 0");

$badSightingDates = performQuery("Find Bad Sighting Dates",
    "SELECT sighting.*, sighting.id AS sightingid
      FROM sighting
      WHERE trip_id = 0");

$badSightingLocations = performQuery("Find Bad Sighting Locations",
    "SELECT sighting.*, sighting.id AS sightingid
      FROM sighting
      WHERE location_id = 0");


$missingLatLong = performQuery("Find Missing Lat/Long",
    "SELECT * FROM location WHERE Latitude=0 OR Longitude=0");

htmlHead("Bad Records");

$request = new Request;

$request->globalMenu();
?>

    <div id="topright-trip">
	  <div class="pagekind">Index</div>
	  <div class="pagetitle">Bad Records</div>
	</div>

    <div id="contentright">

<div class="heading">sightings with bad abbreviations</div>

<?
while($sightingInfo = mysql_fetch_array($badAbbrevs))
{
	$locationInfo = getLocationInfo($sightingInfo["location_id"]);
	$tripInfo = getTripInfo($sightingInfo["trip_id"]); ?>
    <a href="./sightingedit.php?sightingid=<?= $sightingInfo["sightingid"] ?>">
        <?= $locationInfo["Name"] ?> <?= $tripInfo["Date"] ?> sighting #<?= $sightingInfo["id"] ?>
    </a><br>
<?	
}
?>

<div class="heading">sightings with bad trip dates</div>

<?
while($sightingInfo = mysql_fetch_array($badSightingDates))
{
	if ($sightingInfo["Name"] == "") { ?>
        <a href="./sightingedit.php?id=<?= $sightingInfo["sightingid"] ?>">
        <?= $sightingInfo["SpeciesAbbreviation"] ?> <?= $sightingInfo["LocationName"] ?> <?= $sightingInfo["TripDate"] ?>
        </a><br>
 <?	} else {
		break;
	}
}
?>

<div class="heading">sightings with bad location names</div>

<?
while($sightingInfo = mysql_fetch_array($badSightingLocations))
{
	if ($sightingInfo["Name"] == "") { ?>
        <a href="./sightingedit.php?id=<?= $sightingInfo["sightingid"] ?>">
        <?= $sightingInfo["SpeciesAbbreviation"] ?> <?= $sightingInfo["LocationName"] ?> <?= $sightingInfo["TripDate"] ?>
        </a><br>
 <?	} else {
		break;
	}
}
?>

<div class="heading">locations without lat or long</div>

<?
while($locationInfo = mysql_fetch_array($missingLatLong))
{
	echo "<a href=\"./locationcreate.php?locationid=" . $locationInfo["id"] . "\">" . $locationInfo["Name"] . "</a><br/>\n";
}

footer();
?>

</div>

<?
htmlFoot();
?>
