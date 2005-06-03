
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$request = new Request;

$info = $request->getStateInfo();

htmlHead($info["Name"]);

globalMenu();
navTrailLocations($request->getView());

$locationQuery = new LocationQuery($request);
$extrema = $locationQuery->findExtrema();

?>

    <div class=contentright>
      <? stateBrowseButtons($request->getStateID(), $request->getView()); ?>
      <div class="titleblock">	  
<?    if ($request->getView() != "map") rightThumbnailState($info["Abbreviation"]); ?>
	  <div class=pagetitle><?= $info["Name"] ?></div>
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
