
<?php

require("./birdwalker.php");


$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$locationCount = performCount("select count(distinct location.objectid) from location where State='" . $abbrev . "'");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $stateName ?></title>
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
<?    rightThumbnailState("$abbrev"); ?>
	  <div class=pagetitle><?= $stateName ?></div>
      <div class=metadata>
        <? stateViewLinks($abbrev) ?>
      </div>
    </div>

<div class=heading><?= $locationCount ?> Locations</div>

<table columns=10 class="report-content" width="100%">

<? formatLocationByMonthTable(
    "WHERE sighting.LocationName=location.Name AND State='" . $abbrev . "'",
    "./specieslist.php?"); ?>

</table>

    </div>
  </body>
</html>
