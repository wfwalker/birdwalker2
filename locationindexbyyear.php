
<?php

require("./birdwalker.php");

$locationCount = performCount("SELECT COUNT(DISTINCT location.objectid) FROM location");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Locations</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailLocations();
pageThumbnail("SELECT *, rand() AS shuffle FROM sighting WHERE Photo='1' ORDER BY shuffle");
?>

<div class=contentright>
  <div class="titleblock">	  
    <div class=pagetitle>Locations</div>
    <div class=pagesubtitle><?= $locationCount ?> Locations</div>
    <div class=metadata><a href="./locationindex.php">list</a> | by year</div>
  </div>

  <table columns=10 class="report-content" width="100%">

<?
$gridQueryString="
    SELECT distinct(LocationName), County, State, location.objectid AS locationid, bit_or(1 << (year(TripDate) - 1995)) AS mask
      FROM sighting, location
      WHERE sighting.LocationName=location.Name
      GROUP BY sighting.LocationName
      ORDER BY location.State, location.County, location.Name;";

formatLocationByYearTable($gridQueryString, "./specieslist.php?");

?>

  </table>

</div>
</body>
</html>
