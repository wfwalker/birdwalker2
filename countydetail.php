<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$request->getCounty() == "" && die("Fatal error: missing county");

htmlHead($request->getCounty() . " County");

$request->globalMenu();

$locationQuery = new LocationQuery($request);
$stateInfo =  $request->getStateInfo();

$biggestCountyDay = performOneRowQuery("biggest county day",
   "SELECT trips.*, year(trips.Date) as year, DATE_FORMAT(trips.Date, '%M') as month, COUNT(DISTINCT(sightings.species_id)) AS count
      FROM sightings,trips,locations
      WHERE sightings.trip_id=trips.id AND sightings.location_id=locations.id AND locations.County='" . $request->getCounty() . "'
      GROUP BY trips.Date
      ORDER BY count DESC LIMIT 1");

?>

    <div id="topright-location">
	  <div class="pagekind">County Detail</div>
	  <div class="pagetitle"> <?= $request->getCounty() ?> County</div>
	  <div class="pagesubtitle"> <?= $stateInfo["name"] ?></div>
<?    $request->viewLinks("species"); ?>
	</div>

    <div id="contentright">

  <div class="heading">Biggest county day: <?= $biggestCountyDay["month"]?> <?= $biggestCountyDay["year"]?></div>
  <div class="onecolumn">
    <a href="./tripdetail.php?tripid=<?= $biggestCountyDay["id"] ?>"><?= $biggestCountyDay["name"]; ?></a>, <?= $biggestCountyDay["count"] ?> species
  </div>

<?
      $request->handleStandardViews();
      footer();
?>
    </div>

<?
htmlFoot();
?>
