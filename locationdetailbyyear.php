
<?php

require("./birdwalker.php");

$locationID = $_GET['id'];
$siteInfo = getLocationInfo($locationID);

$speciesCount = performCount("
    SELECT COUNT(DISTINCT species.objectid)
      FROM species, sighting
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
      AND sighting.LocationName='" . $siteInfo["Name"]. "'");


$tripCount = performCount("
    SELECT count(DISTINCT trip.objectid)
      FROM trip, sighting
      WHERE sighting.LocationName='" . $siteInfo["Name"]. "'
      AND sighting.TripDate=trip.Date");

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
locationBrowseButtons($siteInfo, $locationID, "byyear");
navTrailLocationDetail($siteInfo);
?>

<div class="contentright">
  <div class="titleblock">
<?  rightThumbnailLocation($siteInfo["Name"]); ?>
    <div class=pagetitle><?= $siteInfo["Name"] ?><? editLink("./locationcreate.php?id=" . $locationID); ?></div>

<?   referenceURL($siteInfo);
      mapLink($siteInfo);
      locationViewLinks($locationID); ?>

    </div>

    <p class=sighting-notes><?= $siteInfo["Notes"] ?></p>
  
  <div class=heading>
          <?= $speciesCount ?> species,
          <?= $tripCount ?> trips<? if ($locationFirstSightings > 0) {  echo ','; ?>
          <?= $locationFirstSightings ?> first sighting<? if ($locationFirstSightings > 1) echo 's'; } ?>
  </div>

<?  $annualLocationTotal = performQuery("
        SELECT COUNT(DISTINCT sighting.SpeciesAbbreviation) AS count, year(sighting.TripDate) AS year
          FROM sighting, location
          WHERE sighting.LocationName='" . $siteInfo["Name"] . "'
          GROUP BY year");

    formatSpeciesByYearTable(
        "WHERE sighting.LocationName='" . $siteInfo["Name"] . "' AND sighting.SpeciesAbbreviation=species.Abbreviation",
        "&locationid=" . $siteInfo["objectid"], $annualLocationTotal); ?>

</div>
</body>
</html>
