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
   "SELECT trip.*, year(trip.Date) as year, DATE_FORMAT(trip.Date, '%M') as month, COUNT(DISTINCT(sighting.species_id)) AS count
      FROM sighting,trip,location
      WHERE sighting.trip_id=trip.id AND sighting.location_id=location.id AND location.County='" . $request->getCounty() . "'
      GROUP BY trip.Date
      ORDER BY count DESC LIMIT 1");

?>

    <div id="topright-location">
	  <div class="pagekind">County Detail</div>
	  <div class="pagetitle"> <?= $request->getCounty() ?> County</div>
	  <div class="pagesubtitle"> <?= $stateInfo["Name"] ?></div>
<?    $request->viewLinks("species"); ?>
	</div>

    <div id="contentright">

  <div class="heading">Biggest county day: <?= $biggestCountyDay["month"]?> <?= $biggestCountyDay["year"]?></div>
  <div class="onecolumn">
    <a href="./tripdetail.php?tripid=<?= $biggestCountyDay["id"] ?>"><?= $biggestCountyDay["Name"]; ?></a>, <?= $biggestCountyDay["count"] ?> species
  </div>

<?
      $request->handleStandardViews();
      footer();
?>
    </div>

<?
htmlFoot();
?>
