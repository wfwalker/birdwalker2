
<?php

require("./birdwalker.php");
require("./speciesquery.php");
require("./sightingquery.php");
require("./tripquery.php");

$locationID = param($_GET, 'id', 1);
$view = param($_GET, 'view', 'list');

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
locationBrowseButtons("./locationdetail.php", $locationID, $view);
navTrailLocationDetail($siteInfo);
?>

<div class="contentright">
  <div class="titleblock">
<?  if ($view != "photo") { rightThumbnailLocation($siteInfo["Name"]); } ?>
    <div class=pagetitle>
        <?= $siteInfo["Name"] ?>
        <? editLink("./locationcreate.php?id=" . $locationID); ?>
    </div>

<?    referenceURL($siteInfo);
      mapLink($siteInfo); ?>
      species: <a href="./locationdetail.php?id=<?=$locationID?>">list</a> |
      <a href="./locationdetail.php?view=bymonth&id=<?=$locationID?>">by month</a> |
      <a href="./locationdetail.php?view=byyear&id=<?=$locationID?>">by year</a> |
      <a href="./locationdetail.php?view=photo&id=<?=$locationID?>">photos</a>
    </div>

    <p class=sighting-notes><?= $siteInfo["Notes"] ?></p>

<?
	if ($view == "list")
	{
		countHeading($tripCount, "trip");
		$tripQuery->formatTwoColumnTripList();
		doubleCountHeading($speciesQuery->getSpeciesCount(), "species", $locationFirstSightings, "life bird");
		$speciesQuery->formatTwoColumnSpeciesList();
	}
	else if ($view == "bymonth")
	{
		doubleCountHeading($speciesQuery->getSpeciesCount(), "species", $locationFirstSightings, "life bird");
		$speciesQuery->formatSpeciesByMonthTable();
	}
	else if ($view == "byyear")
	{
		doubleCountHeading($speciesQuery->getSpeciesCount(), "species", $locationFirstSightings, "life bird");
		$speciesQuery->formatSpeciesByYearTable();
	}
	else if ($view == "photo")
	{
		$sightingQuery = new SightingQuery;
		$sightingQuery->setLocationID($locationID);
		$sightingQuery->formatPhotos();
	}

?>

</div>
</body>
</html>
