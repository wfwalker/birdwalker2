
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

$items[] = "
    <a href=\"./statelocations.php?state=" .  $siteInfo["State"] . "\">" .
      strtolower(getStateNameForAbbreviation($siteInfo["State"])) . "
    </a>";
$items[] = "
    <a href=\"./countylocations.php?county=" . $siteInfo["County"] . "&state=" . $siteInfo["State"] . "\">" .
      strtolower($siteInfo["County"]) . " county
    </a>";
$items[] =
    strtolower($siteInfo["Name"]);

navTrailLocations($items);
pageThumbnail("
    SELECT *, rand() AS shuffle
      FROM sighting
      WHERE Photo='1' AND LocationName='" . $siteInfo["Name"] . "'
      ORDER BY shuffle");
?>

<div class="contentright">
  <div class="titleblock">
    <div class=pagetitle><?= $siteInfo["Name"] ?></div>

<? referenceURL($siteInfo); ?>

<? if (getEnableEdit()) { ?>
	<div><a href="./locationcreate.php?id=<?= $locationID ?>">edit</a></div>
<? }

      mapLink($siteInfo);
      locationViewLinks($locationID); ?>
    </div>

    <p class=sighting-notes><?= $siteInfo["Notes"] ?></p>
  
  <div class=heading>
          <?= $speciesCount ?> species,
          <?= $tripCount ?> trips<? if ($locationFirstSightings > 0) {  echo ','; ?>
          <?= $locationFirstSightings ?> first sighting<? if ($locationFirstSightings > 1) echo 's'; } ?>
  </div>

<?   $gridQueryString="
        SELECT DISTINCT(CommonName), species.objectid AS speciesid, bit_or(1 << (year(TripDate) - 1995)) AS mask
          FROM sighting, species
          WHERE sighting.LocationName='" . $siteInfo["Name"] . "' AND sighting.SpeciesAbbreviation=species.Abbreviation
          GROUP BY sighting.SpeciesAbbreviation
          ORDER BY speciesid";

	  $annualLocationTotal = performQuery("
        SELECT COUNT(DISTINCT sighting.SpeciesAbbreviation) AS count, year(sighting.TripDate) AS year
          FROM sighting, location
          WHERE sighting.LocationName='" . $siteInfo["Name"] . "'
          GROUP BY year");

	  formatSpeciesByYearTable($gridQueryString, "&locationid=" . $siteInfo["objectid"], $annualLocationTotal); ?>

</div>
</body>
</html>
