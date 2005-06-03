
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./sightingquery.php");
require_once("./map.php");
require_once("./tripquery.php");

$request = new Request;

$speciesInfo = getSpeciesInfo($request->getSpeciesID());

$locationQuery = new LocationQuery($request);
$extrema = $locationQuery->findExtrema();

if ($request->getView() != "photo")
{
	$tripQuery = new TripQuery($request);
	$tripCount = $tripQuery->getTripCount();
	
	$locationCount = $locationQuery->getLocationCount();
}

htmlHead($speciesInfo["CommonName"]);
globalMenu();
$request->navTrailBirds();
?>

  <div class=contentright>
    <? speciesBrowseButtons("./speciesdetail.php", $request->getSpeciesID(), $request->getView()); ?>

	<div class="titleblock">
<?    if (($request->getView() != "map") && ($request->getView() != "photo")) { rightThumbnailSpecies($speciesInfo["Abbreviation"]); } ?>
      <div class="pagetitle">
        <?= $speciesInfo["CommonName"] ?>
        <?= editlink("./speciesedit.php?speciesid=" . $request->getSpeciesID()) ?>
      </div>
      <div class=metadata>
        <?= $speciesInfo["LatinName"] ?><br/>
<?  if (strlen($speciesInfo["ReferenceURL"]) > 0) { ?>
        <div><a href="<?= $speciesInfo["ReferenceURL"] ?>">See also...</a></div>
<?  }
    if ($speciesInfo["ABACountable"] == '0') { ?>
        <div>NOT ABA COUNTABLE</div>
<?  } ?>
      <?= $request->linkToSelfChangeView("lists", "lists") ?> |
      <?= $request->linkToSelfChangeView("locationsbymonth", "by month") ?> |
      <?= $request->linkToSelfChangeView("locationsbyyear", "by year") ?> |
      <?= $request->linkToSelfChangeView("photo", "photo") ?> |
      <?= $request->linkToSelfChangeView("map", "map") ?><br/>
      </div>
   </div>

   <div class=report-content><?= $speciesInfo["Notes"] ?></div>

<?
$request->handleStandardViews("locations");
footer();
?>

   </div>

<?
htmlFoot();
?>