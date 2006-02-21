
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$request->getCounty() == "" && die("Fatal error: missing county");

htmlHead($request->getCounty() . " County");

$request->globalMenu();

$locationQuery = new LocationQuery($request);
$extrema = $locationQuery->findExtrema();
$stateInfo =  $request->getStateInfo();

?>

    <div class="topright-location">
	  <div class="pagekind">County Detail</div>
	  <div class="pagetitle"> <?= $request->getCounty() ?> County</div>
	  <div class="pagesubtitle"> <?= $stateInfo["Name"] ?></div>
<?    $request->viewLinks("species"); ?>
	</div>

    <div class=contentright>
      <div class="titleblock">	  
      </div>
<?

$request->handleStandardViews();

footer();

?>

    </div>

<?
htmlFoot();
?>
