
<?php

require("./birdwalker.php");

$locationID = $_GET['id'];
$siteInfo = getLocationInfo($locationID);

$speciesCount = performCount("
    SELECT COUNT(DISTINCT species.objectid)
      FROM species, sighting
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
      AND sighting.LocationName='" . $siteInfo["Name"]. "'");

$tripQuery = performQuery("
    SELECT DISTINCT trip.objectid, trip.*, date_format(Date, '%M %e, %Y') AS niceDate,
      COUNT(DISTINCT sighting.SpeciesAbbreviation) AS tripCount
      FROM trip, sighting
      WHERE sighting.LocationName='" . $siteInfo["Name"]. "'
      AND sighting.TripDate=trip.Date
      GROUP BY trip.Date
      ORDER BY trip.Date DESC");

$tripCount = mysql_num_rows($tripQuery);

$locationSightings = performQuery("
    SELECT sighting.objectid FROM sighting, location
      WHERE sighting.LocationName=location.Name
      AND location.objectid='" . $locationID ."'");

$firstSightings = getFirstSightings();
$locationFirstSightings = 0;

while($sightingInfo = mysql_fetch_array($locationSightings)) {
	if ($firstSightings[$sightingInfo['objectid']] != null) {
		$locationFirstSightings++;
	}
}

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $siteInfo["Name"] ?></title>
</head>

<body>

<?php
globalMenu();
locationBrowseButtons($siteInfo, $locationID, "");
navTrailLocationDetail($siteInfo);
?>

<div class="contentright">
  <div class="titleblock">
<?  rightThumbnailLocation($siteInfo["Name"]); ?>
    <div class=pagetitle>
        <?= $siteInfo["Name"] ?>
        <? editLink("./locationcreate.php?id=" . $locationID); ?>
    </div>

<?    referenceURL($siteInfo);
      mapLink($siteInfo);
      locationViewLinks($locationID); ?>
    </div>

    <p class=sighting-notes><?= $siteInfo["Notes"] ?></p>

     <div class="heading">
         <?= $tripCount ?> trip<? if ($tripCount > 1) echo 's' ?>
     </div>

<? formatTwoColumnTripList($tripQuery); ?>


   <div class=heading>
	 <?= $speciesCount ?> species<? if ($locationFirstSightings > 0) { ?>,
     <?= $locationFirstSightings ?> first sighting<? if ($locationFirstSightings > 1) echo 's'; } ?>
   </div>

<? formatTwoColumnSpeciesList(performQuery("
        SELECT distinct(species.objectid), species.CommonName, sighting.objectid as sightingid
          FROM species, sighting
          WHERE species.Abbreviation=sighting.SpeciesAbbreviation
          AND sighting.LocationName='" . $siteInfo["Name"]. "'
          ORDER BY species.objectid")); ?>

</div>
</body>
</html>
