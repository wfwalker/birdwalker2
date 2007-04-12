<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$request = new Request;

$info = $request->getStateInfo();

htmlHead($info["Name"]);

$request->globalMenu();


$biggestStateDay = performOneRowQuery("biggest state day",
   "SELECT trip.*, year(TripDate) as year, DATE_FORMAT(TripDate, '%M') as month, COUNT(DISTINCT(sighting.SpeciesAbbreviation)) AS count
      FROM sighting,trip,location
      WHERE sighting.TripDate=trip.Date AND sighting.LocationName=location.Name AND location.State='" . $info["Abbreviation"] . "'
      GROUP BY trip.Date
      ORDER BY count DESC LIMIT 1");

?>

    <div id="topright-location">
      <? stateBrowseButtons($request->getStateID(), $request->getView()); ?>
	  <div class="pagetitle"><?= $info["Name"] ?></div>
<?    $request->viewLinks("species"); ?>
	</div>

    <div id="contentright">

  <div class="heading">Biggest state day: <?= $biggestStateDay["month"]?> <?= $biggestStateDay["year"]?></div>
  <div class="onecolumn">
    <a href="./tripdetail.php?tripid=<?= $biggestStateDay["objectid"] ?>"><?= $biggestStateDay["Name"]; ?></a>, <?= $biggestStateDay["count"] ?> species
  </div>



<?
      $request->handleStandardViews();
      footer();
?>
    </div>

<?
htmlFoot();
?>
