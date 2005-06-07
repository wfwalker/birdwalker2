
<?php

require_once("./birdwalker.php");

$badAbbrevs = performQuery("
    SELECT species.*,sighting.*, sighting.objectid AS sightingid
      FROM sighting
      LEFT JOIN species ON species.Abbreviation=sighting.SpeciesAbbreviation
      ORDER BY species.CommonName, sighting.SpeciesAbbreviation");

$badSightingDates = performQuery("
    SELECT trip.*,sighting.*, sighting.objectid AS sightingid
      FROM sighting
      LEFT JOIN trip ON trip.Date=sighting.TripDate
      ORDER BY trip.Name, sighting.TripDate");

$badSightingLocations = performQuery("
    SELECT location.*,sighting.*, sighting.objectid AS sightingid
      FROM sighting
      LEFT JOIN location ON location.Name=sighting.LocationName
      ORDER BY location.County, sighting.LocationName");


$missingLatLong = performQuery("
    SELECT * FROM location WHERE Latitude=0 OR Longitude=0");

htmlHead("Bad Records");

globalMenu();
navTrail();
?>

    <div class=contentright>
	  <div class=pagesubtitle>Index</div>
      <div class="titleblock">	  
	  <div class=pagetitle>Bad Records</div>
      </div>

<div class=heading>sightings with bad abbreviations</div>

<?
while($sightingInfo = mysql_fetch_array($badAbbrevs))
{
	if ($sightingInfo["CommonName"] == "") { ?>
        <a href="./sightingedit.php?id=<?= $sightingInfo["sightingid"] ?>">
        <?= $sightingInfo["SpeciesAbbreviation"] ?> <?= $sightingInfo["LocationName"] ?> <?= $sightingInfo["TripDate"] ?>
        </a><br>
 <?	} else {
		break;
	}
}
?>

<div class=heading>sightings with bad trip dates</div>

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

<div class=heading>sightings with bad location names</div>

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

<div class=heading>locations without lat or long</div>

<?
while($locationInfo = mysql_fetch_array($missingLatLong))
{
	echo "<a href=\"./locationcreate.php?id=" . $locationInfo["objectid"] . "\">" . $locationInfo["Name"] . "</a><br/>\n";
}

footer();
?>

</div>

<?
htmlFoot();
?>