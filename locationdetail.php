
<?php

require("./speciesQuery.php");

$locationID = param($_GET, 'id', 1);
$siteInfo = getLocationInfo($locationID);

$speciesQuery = new SpeciesQuery;

$speciesQuery->setLocationID($locationID);

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

   <? formatTwoColumnTripList($tripQuery, $firstSightings); ?>

   <div class=heading>
     <?= $speciesQuery->getSpeciesCount() ?> species<? if ($locationFirstSightings > 0) { ?>,
     <?= $locationFirstSightings ?> life bird<? if ($locationFirstSightings > 1) echo 's'; } ?>
   </div>

<? $speciesQuery->formatTwoColumnSpeciesList(); ?>

</div>
</body>
</html>
