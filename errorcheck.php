
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$badAbbrevs = performQuery("Find Bad Abbreviations",
    "SELECT species.*,sighting.*, sighting.objectid AS sightingid
      FROM sighting
      LEFT JOIN species ON species.Abbreviation=sighting.SpeciesAbbreviation
      ORDER BY species.CommonName, sighting.SpeciesAbbreviation");

$badSightingDates = performQuery("Find Bad Sighting Dates",
    "SELECT trip.*,sighting.*, sighting.objectid AS sightingid
      FROM sighting
      LEFT JOIN trip ON trip.Date=sighting.TripDate
      ORDER BY trip.Name, sighting.TripDate");

$badSightingLocations = performQuery("Find Bad Sighting Locations",
    "SELECT location.*,sighting.*, sighting.objectid AS sightingid
      FROM sighting
      LEFT JOIN location ON location.Name=sighting.LocationName
      ORDER BY location.County, sighting.LocationName");


$missingLatLong = performQuery("Find Missing Lat/Long",
    "SELECT * FROM location WHERE Latitude=0 OR Longitude=0");

htmlHead("Bad Records");

$request = new Request;

$request->globalMenu();
?>

    <div class="topright">
	  <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
	  <div class="pagetitle">Bad Records</div>
      </div>
	</div>

    <div class="contentright">

<div class="heading">sightings with bad abbreviations</div>

<?
while($sightingInfo = mysql_fetch_array($badAbbrevs))
{
	if ($sightingInfo["CommonName"] == "") { ?>
        <a href="./sightingedit.php?sightingid=<?= $sightingInfo["sightingid"] ?>">
        <?= $sightingInfo["SpeciesAbbreviation"] ?> <?= $sightingInfo["LocationName"] ?> <?= $sightingInfo["TripDate"] ?>
        </a><br>
 <?	} else {
		break;
	}
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
	echo "<a href=\"./locationcreate.php?locationid=" . $locationInfo["objectid"] . "\">" . $locationInfo["Name"] . "</a><br/>\n";
}

footer();
?>

</div>

<?
htmlFoot();
?>