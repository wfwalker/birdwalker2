
<?php

require("./birdwalker.php");

$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$stateListCount =  performCount("
    SELECT COUNT(DISTINCT species.objectid)
      FROM species, sighting, location
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND sighting.LocationName=location.Name
      AND location.State='" . $abbrev . "'");

$annualStateTotal = performQuery("
    SELECT COUNT(DISTINCT sighting.SpeciesAbbreviation) AS count, month(sighting.TripDate) AS month
      FROM sighting, location WHERE sighting.LocationName=location.Name AND location.State='" . $abbrev . "'
      GROUP BY month"); ?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $stateName ?> State List</title>
  </head>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
$items[] = strtolower($stateName);
navTrailLocations($items);
?>

    <div class=contentright>
	  <div class="titleblock">
<?      rightThumbnailState("$abbrev"); ?>
        <div class=pagetitle><?= $stateName ?></div>
        <div class=pagesubtitle> <?= $stateListCount ?> species</div>
      <div class=metadata>
        <? stateViewLinks($abbrev) ?>
      </div>
      </div>

<?
$gridQueryString="
    SELECT DISTINCT(CommonName), species.objectid AS speciesid, bit_or(1 << month(TripDate)) AS mask
      FROM sighting, species, location
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation AND sighting.LocationName=location.Name
      AND location.State='". $abbrev . "'
      GROUP BY sighting.SpeciesAbbreviation ORDER BY speciesid";

formatSpeciesByMonthTable($gridQueryString, "&state=" . $abbrev, $annualStateTotal);

?>

    </div>
  </body>
</html>
