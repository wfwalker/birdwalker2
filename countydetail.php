
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$request->getCounty() == "" && die("Fatal error: missing county");

htmlHead($request->getCounty() . " County");

globalMenu();

$request->navTrailLocations();

$locationQuery = new LocationQuery($request);
$extrema = $locationQuery->findExtrema();

?>

    <div class=contentright>
	<? disabledBrowseButtons("County Detail"); ?>
      <div class="titleblock">	  
<?    if (($request->getView() != "map") && ($request->getView() != "photo")) { rightThumbnailCounty($request->getCounty()); } ?>
	  <div class=pagetitle> <?= $request->getCounty() ?> County</div>


<?    $request->viewLinks(); ?>

    </div>

<?

$request->handleStandardViews("species");

footer();

?>

    </div>

<?
htmlFoot();
?>
