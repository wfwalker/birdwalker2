
<?php

require_once("./birdwalker.php");
require_once("./map.php");

$view = param($_GET, "view", "locations");

$locationQuery = new LocationQuery;
$extrema = $locationQuery->findExtrema();

htmlHead("Locations");

globalMenu();
navTrailLocations($view);
?>

    <div class=contentright>
	  <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
<?    if ($view != "map" ) rightThumbnailAll(); ?>
	  <div class=pagetitle>Locations</div>
    <div class=metadata>
      <a href="./locationindex.php?view=locations">list</a> |
      <a href="./locationindex.php?view=locationsbymonth">by month</a> |
	  <a href="./locationindex.php?view=locationsbyyear">by year<a/> |
      <a href="./locationindex.php?view=map&minlat=<?= $extrema["minLat"]-0.01 ?>&maxlat=<?= $extrema["maxLat"]+0.01 ?>&minlong=<?= $extrema["minLong"]-0.01 ?>&maxlong=<?= $extrema["maxLong"]+0.01 ?>">map</a><br/>
    </div>
	</div>

<? if ($view == "locations") {
	$locationQuery->formatTwoColumnLocationList($view, true);
   } else if ($view == "locationsbymonth") {
      $locationQuery->formatLocationByMonthTable();
   } else if ($view == "locationsbyyear") {
      $locationQuery->formatLocationByYearTable();
   } else if ($view == "map") {
      $map = new Map("./locationindex.php");
	  $map->setFromRequest($_GET);
      $map->draw();
   }

footer();
?>

    </div>

<?
htmlFoot();
?>
