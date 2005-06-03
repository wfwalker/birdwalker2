
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./chronolist.php");
require_once("./sightingquery.php");
require_once("./tripquery.php");

$request = new Request;

htmlHead($request->getCounty() . " County");

globalMenu();

$stateInfo = getStateInfo($request->getStateID());
$stateName = $stateInfo["Name"];

$items[]="<a href=\"./statedetail.php?view=" . $request->getView() . "&stateid=" . $stateInfo["objectid"] . "\">" . strtolower($stateInfo["Name"]) . "</a>";
navTrailLocations($request->getView(), $items);

$locationQuery = new LocationQuery($request);
$extrema = $locationQuery->findExtrema();

?>

    <div class=contentright>
	<? disabledBrowseButtons("County Detail"); ?>
      <div class="titleblock">	  
<?    if (($request->getView() != "map") && ($request->getView() != "photo")) { rightThumbnailCounty($request->getCounty()); } ?>
	  <div class=pagetitle> <?= $request->getCounty() ?> County</div>
      <div class=metadata>
        locations:
		<?= $request->linkToSelfChangeView("locations", "list") ?> | 
		<?= $request->linkToSelfChangeView("locationsbymonth", "by month") ?> | 
		<?= $request->linkToSelfChangeView("locationsbyyear", "by year") ?> | 
		<?= $request->linkToSelfChangeView("map", "map") ?><br/>
        species:	
		<?= $request->linkToSelfChangeView("species", "list") ?> | 
		<?= $request->linkToSelfChangeView("chrono", "ABA") ?> | 
		<?= $request->linkToSelfChangeView("speciesbymonth", "by month") ?> | 
		<?= $request->linkToSelfChangeView("speciesbyyear", "by year") ?> | 
		<?= $request->linkToSelfChangeView("photo", "photo") ?><br/>
      </div>
    </div>

<?

$request->handleStandardViews("species");

footer();

?>

    </div>

<?
htmlFoot();
?>
