
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
$gridQueryString=" select distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, location where sighting.LocationName=location.Name and State='" . $abbrev . "' group by sighting.LocationName order by location.State, location.County, location.Name;";

formatLocationByYearTable($gridQueryString, "./specieslist.php?");

?>

</table>

    </div>
  </body>
</html>
