
<?php

require("./birdwalker.php");

$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$stateListCount =  performCount("select count(distinct species.objectid) from species, sighting, location where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.State='" . $abbrev . "'");

$annualStateTotal = performQuery("select count(distinct sighting.SpeciesAbbreviation) as count, year(sighting.TripDate) as year from sighting, location where sighting.LocationName=location.Name and location.State='" . $abbrev . "' group by year");
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
    "WHERE sighting.SpeciesAbbreviation=species.Abbreviation AND sighting.LocationName=location.Name AND location.State='". $abbrev . "'",
        "&state=" . $abbrev,
        $annualStateTotal); ?>

    </div>
  </body>
</html>
