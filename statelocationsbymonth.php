
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
        <div class=pagesubtitle><?= $locationCount ?> Locations</div>
      <div class=metadata>
        <? stateViewLinks($abbrev) ?>
      </div>

      </div>

<table columns=10 class="report-content" width="100%">

<?
$gridQueryString="
    SELECT DISTINCT(LocationName), County, State, location.objectid AS locationid, bit_or(1 << month(TripDate)) AS mask
      FROM sighting, location
      WHERE sighting.LocationName=location.Name AND State='" . $abbrev . "'
      GROUP BY sighting.LocationName
      ORDER BY location.State, location.County, location.Name;";

formatLocationByMonthTable($gridQueryString, "./specieslist.php?");

?>

</table>

    </div>
  </body>
</html>
