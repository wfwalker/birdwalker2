
<?php

require("./birdwalker.php");

$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$stateListCount =  performCount("
    SELECT COUNT(DISTINCT species.objectid)
      FROM species, sighting, location
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
        AND sighting.LocationName=location.Name AND location.State='" . $abbrev . "'");

$annualStateTotal = performQuery("
    SELECT COUNT(DISTINCT species.objectid) AS count, year(sighting.TripDate) AS year
      FROM sighting, species, location
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
        AND sighting.LocationName=location.Name AND location.State='" . $abbrev . "'
      GROUP BY year");
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $stateName ?> State List</title>
  </head>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailLocations();
?>

    <div class=contentright>
	  <div class="titleblock">
<?      rightThumbnailState("$abbrev"); ?>
        <div class=pagetitle><?= $stateName ?></div>
      <div class=metadata>
        <? stateViewLinks($abbrev) ?>
      </div>
      </div>

<div class=heading> <?= $stateListCount ?> species</div>

<? formatSpeciesByYearTable(
    "WHERE sighting.SpeciesAbbreviation=species.Abbreviation
        AND sighting.LocationName=location.Name AND location.State='". $abbrev . "'",
        "&state=" . $abbrev,
        $annualStateTotal); ?>

    </div>
  </body>
</html>
