
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
$request->globalMenu();

?>

  <div class="topright">
    <? speciesBrowseButtons("./speciesdetail.php", $request->getSpeciesID(), $request->getView()); ?>

      <div class="pagetitle">
        <?= $speciesInfo["CommonName"] ?>
        <?= editlink("./speciesedit.php?speciesid=" . $request->getSpeciesID()) ?>
      </div>
      <div class="pagesubtitle">
        <?= $speciesInfo["LatinName"] ?>
	  </div>
  </div>

  <div class="contentright">
    <?= $request->viewLinks("locations"); ?>

	<div class="titleblock">
	  <div class="metadata">
<?  if (strlen($speciesInfo["ReferenceURL"]) > 0) { ?>
        <div><a href="<?= $speciesInfo["ReferenceURL"] ?>">See also...</a></div>
<?  }
    if ($speciesInfo["ABACountable"] == '0') { ?>
        <div>NOT ABA COUNTABLE</div>
<?  } ?>
      </div>
   </div>
<?
   if ($speciesInfo["Notes"] != "") { ?>
	<div class="heading">Notes</div>
    <div class=report-content><?= $speciesInfo["Notes"] ?></div>
<? }

$request->handleStandardViews();
footer();
?>

   </div>

<?
htmlFoot();
?>