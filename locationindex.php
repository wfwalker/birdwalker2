
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./map.php");

$request = new Request;

$locationQuery = new LocationQuery($request);
$extrema = $locationQuery->findExtrema();

htmlHead("Locations");

globalMenu();
navTrailLocations($request->getView());
?>

    <div class=contentright>
	  <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
<?    if ($request->getView() != "map" ) rightThumbnailAll(); ?>
	  <div class=pagetitle>Locations</div>
    <div class=metadata>
      <a href="./locationindex.php?view=locations">list</a> |
      <a href="./locationindex.php?view=locationsbymonth">by month</a> |
	  <a href="./locationindex.php?view=locationsbyyear">by year<a/> |
      <a href="./locationindex.php?view=map&minlat=<?= $extrema["minLat"]-0.01 ?>&maxlat=<?= $extrema["maxLat"]+0.01 ?>&minlong=<?= $extrema["minLong"]-0.01 ?>&maxlong=<?= $extrema["maxLong"]+0.01 ?>">map</a><br/>
    </div>
	</div>

<br clear="all"/>

<?
	  if ($request->getView() == "locations" || $request->getView() == "") {
		  $locationQuery->formatTwoColumnLocationList($request->getView(), true);
	  } else if ($request->getView() == "locationsbymonth") {
		  $locationQuery->formatLocationByMonthTable();
	  } else if ($request->getView() == "locationsbyyear") {
		  $locationQuery->formatLocationByYearTable();
	  } else if ($request->getView() == "map") {
		  $map = new Map("./locationindex.php", $request);
		  $map->draw();
	  }

footer();
?>

    </div>

<?
htmlFoot();
?>
