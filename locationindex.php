
<?php

require("./birdwalker.php");
require("./locationquery.php");

$view = param($_GET, "view", "");

$locationQuery = new LocationQuery;

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
      <a href="./locationindex.php?view=bymonth">by month</a> |
	  <a href="./locationindex.php?view=byyear">by year<a/>
    </div>
	</div>

    <div class=heading><?= $locationQuery->getLocationCount() ?> Locations</div>

<? if ($view == "") {
	  $locationQuery->formatTwoColumnLocationList();
   } else if ($view == "bymonth") {
      $locationQuery->formatLocationByMonthTable();
   } else if ($view == "byyear") {
      $locationQuery->formatLocationByYearTable();
   } ?>

    </div>
  </body>
</html>
