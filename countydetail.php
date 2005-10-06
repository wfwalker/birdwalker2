
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
	<? disabledBrowseButtons("County Detail"); ?>
	  <div class="pagetitle"> <?= $request->getCounty() ?> County</div>
	  <div class="pagesubtitle"> <?= $stateInfo["Name"] ?></div>
	</div>

    <div class=contentright>
<?    if (($request->getView() != "map") && ($request->getView() != "photo")) { rightThumbnailCounty($request->getCounty()); } ?>

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
