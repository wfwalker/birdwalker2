<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./sightingquery.php");
require_once("./map.php");
require_once("./tripquery.php");

$request = new Request;

$speciesInfo = getSpeciesInfo($request->getSpeciesID());

htmlHead($speciesInfo["common_name"]);
$request->globalMenu();

?>

  <div id="topright-species">
    <? speciesBrowseButtons("./speciesdetail.php", $request->getSpeciesID(), $request->getView()); ?>

      <div class="pagetitle">
        <?= $speciesInfo["common_name"] ?>
        <?= editlink("./speciesedit.php?speciesid=" . $request->getSpeciesID()) ?>
      </div>
      <div class="pagesubtitle">
        <?= $speciesInfo["latin_name"] ?>
	  </div>
      <?= $request->viewLinks("trips"); ?>
  </div>

  <div id="contentright">

<?  if ($speciesInfo["noteworthy"] != "0")
    { ?>
	  <div class="heading">Notes</div>
	  <div class="leftcolumn">
<?    if ($speciesInfo["notes"] != "") { ?>
	      <div class="report-content"><?= stripslashes($speciesInfo["notes"]) ?></div>
<?    } ?>

<?    reference_url($speciesInfo); ?>

<?    if ($speciesInfo["aba_countable"] == '0') { ?>
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
