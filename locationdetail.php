
<?php

require("./birdwalker.php");
require("./speciesquery.php");
require("./tripquery.php");

$locationID = param($_GET, 'id', 1);
$siteInfo = getLocationInfo($locationID);

$speciesQuery = new SpeciesQuery;

$speciesQuery->setLocationID($locationID);

$tripQuery = new TripQuery;
$tripQuery->setLocationID($locationID);
$tripCount = $tripQuery->getTripCount();

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

<?  $tripQuery->formatTwoColumnTripList(); ?>

   <div class=heading>
     <?= $speciesQuery->getSpeciesCount() ?> species<? if ($locationFirstSightings > 0) { ?>,
     <?= $locationFirstSightings ?> life bird<? if ($locationFirstSightings > 1) echo 's'; } ?>
   </div>

<? $speciesQuery->formatTwoColumnSpeciesList(); ?>

</div>
</body>
</html>
