
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

    <div class="topright">
	  <div class="pagesubtitle"><img align="center" src="./images/location.gif"> county detail</div>
	  <div class="pagetitle"> <?= $request->getCounty() ?> County</div>
	  <div class="pagesubtitle"> <?= $stateInfo["Name"] ?></div>
	</div>

    <div class=contentright>
      <div class="titleblock">	  
<?    $request->viewLinks("species"); ?>
      </div>

<?

$request->handleStandardViews();

footer();

?>

    </div>

<?
htmlFoot();
?>
