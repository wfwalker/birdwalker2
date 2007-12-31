<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$request->getCountyID() == "" && die("Fatal error: missing countyid");

$countyInfo = getCountyInfo($request->getCountyID());
htmlHead($countyInfo["name"] . " County");

$request->globalMenu();

$locationQuery = new LocationQuery($request);
$stateInfo =  getStateInfo($countyInfo["state_id"]);

$biggestCountyDay = performOneRowQuery("biggest county day",
   "SELECT trips.*, year(trips.date) as year, DATE_FORMAT(trips.date, '%M') as month, COUNT(DISTINCT(sightings.species_id)) AS count
      FROM sightings,trips,locations
      WHERE sightings.trip_id=trips.id AND sightings.location_id=locations.id AND locations.county_id='" . $request->getCountyID() . "'
      GROUP BY trips.date
      ORDER BY count DESC LIMIT 1");

?>

    <div id="topright-location">
	  <div class="pagekind">County Detail</div>
	  <div class="pagetitle"> <?= $countyInfo["name"] ?> County</div>
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
