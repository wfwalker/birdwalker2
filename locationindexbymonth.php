
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
?>

<div class=contentright>
  <div class="titleblock">	  
<?    rightThumbnailAll(); ?>
    <div class=pagetitle>Locations</div>
    <div class=metadata>
      <a href="./locationindex.php">list</a> |
      <a href="./locationindexbymonth.php">by month</a> |
	  <a href="./locationindexbyyear.php">by year<a/>
    </div>
  </div>

  <div class=heading><?= $locationCount ?> Locations</div>

  <table columns=10 class="report-content" width="100%">

<?
$gridQueryString="
    SELECT distinct(LocationName), County, State, location.objectid AS locationid, bit_or(1 << month(TripDate)) AS mask
      FROM sighting, location
      WHERE sighting.LocationName=location.Name
      GROUP BY sighting.LocationName
      ORDER BY location.State, location.County, location.Name;";

formatLocationByMonthTable($gridQueryString, "./specieslist.php?");

?>

  </table>

</div>
</body>
</html>
