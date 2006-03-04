
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

  <div class="topright-species">
    <? speciesBrowseButtons("./speciesdetail.php", $request->getSpeciesID(), $request->getView()); ?>

      <div class="pagetitle">
        <?= $speciesInfo["CommonName"] ?>
        <?= editlink("./speciesedit.php?speciesid=" . $request->getSpeciesID()) ?>
      </div>
      <div class="pagesubtitle">
        <?= $speciesInfo["LatinName"] ?>
	  </div>
      <?= $request->viewLinks("locations"); ?>
  </div>

  <div class="contentright">

<?  if ($speciesInfo["noteworthy"] != "0")
    { ?>
	  <div class="heading">Notes</div>
	  <div class="onecolumn">
<?    if ($speciesInfo["Notes"] != "") { ?>
        <div class="report-content"><?= $speciesInfo["Notes"] ?></div>
<?    } ?>

<?    referenceURL($speciesInfo); ?>

<?    if ($speciesInfo["ABACountable"] == '0') { ?>
        <div>NOT ABA COUNTABLE</div>
<?    } ?>
      </div>
<?  }

    $request->handleStandardViews();
    footer();
?>
  </div>

<?
htmlFoot();
?>