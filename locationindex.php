
<?php

require_once("./birdwalker.php");
require_once("./map.php");

$view = param($_GET, "view", "list");

$locationQuery = new LocationQuery;
$extrema = $locationQuery->findExtrema();

?>

<html>

  <? htmlHead("Locations"); ?>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailLocations();
?>

    <div class=contentright>
	  <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
<?    if ($view != "map" ) rightThumbnailAll(); ?>
	  <div class=pagetitle>Locations</div>
    <div class=metadata>
      <a href="./locationindex.php">list</a> |
      <a href="./locationindex.php?view=bymonth">by month</a> |
	  <a href="./locationindex.php?view=byyear">by year<a/> |
      <a href="./locationindex.php?view=map&minlat=<?= $extrema["minLat"]-0.01 ?>&maxlat=<?= $extrema["maxLat"]+0.01 ?>&minlong=<?= $extrema["minLong"]-0.01 ?>&maxlong=<?= $extrema["maxLong"]+0.01 ?>">map</a><br/>
    </div>
	</div>

<? if ($view == "list") {
	  $locationQuery->formatTwoColumnLocationList();
   } else if ($view == "bymonth") {
      $locationQuery->formatLocationByMonthTable();
   } else if ($view == "byyear") {
      $locationQuery->formatLocationByYearTable();
   } else if ($view == "map") {
      $map = new Map("./locationindex.php");
	  $map->setFromRequest($_GET);
      $map->draw();
   }?>

    </div>
  </body>
</html>
