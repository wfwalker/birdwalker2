<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");

$locationList = performQuery("Get All Locations", "SELECT Name, id FROM location ORDER BY Name");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);

htmlHead($request->getPagetitle());
$request->globalMenu();

?>


  <div id="topright-species">
	  <div class="pagekind">Species List</div>
	  <div class="pagetitle"> <?= $request->getPagetitle() ?></div>
	  <div class="pagesubtitle"> </div>
  </div>

  <div id="contentright">
<?    $speciesQuery->formatTwoColumnSpeciesList(); ?>
  </div>

<?
htmlFoot();
?>
