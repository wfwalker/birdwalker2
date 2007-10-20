<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$request = new Request;

$info = $request->getStateInfo();

htmlHead($info["name"]);

$request->globalMenu();


$biggestStateDay = performOneRowQuery("biggest state day",
   "SELECT trips.*, year(trips.Date) as year, DATE_FORMAT(trips.Date, '%M') as month, COUNT(DISTINCT(sightings.species_id)) AS count
      FROM sightings, trips, locations
      WHERE sightings.trip_id=trips.id AND sightings.location_id=locations.id AND locations.state='" . $info["abbreviation"] . "'
      GROUP BY trips.Date
      ORDER BY count DESC LIMIT 1");

?>

    <div id="topright-location">
      <? stateBrowseButtons($request->getStateID(), $request->getView()); ?>
	  <div class="pagetitle"><?= $info["name"] ?></div>
<?    $request->viewLinks("species"); ?>
	</div>

    <div id="contentright">

  <div class="heading">Biggest state day: <?= $biggestStateDay["month"]?> <?= $biggestStateDay["year"]?></div>
  <div class="onecolumn">
    <a href="./tripdetail.php?tripid=<?= $biggestStateDay["id"] ?>"><?= $biggestStateDay["name"]; ?></a>, <?= $biggestStateDay["count"] ?> species
  </div>



<?
      $request->handleStandardViews();
      footer();
?>
    </div>

<?
htmlFoot();
?>
