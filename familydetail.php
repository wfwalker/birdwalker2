<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./tripquery.php");
require_once("./locationquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$request = new Request;
$request->setSpeciesID("");

$familyInfo = $request->getFamilyInfo();
$orderInfo = $request->getOrderInfo();

$nextFamily = performCount("Find Next Family",
    "SELECT FLOOR(MIN(species.id)/pow(10,7)) FROM species, sighting
      WHERE sightings.species_id=species.id
      AND species.id >" . ($request->getFamilyID() + 1) * pow(10, 7) . " LIMIT 1");

if ($nextFamily != "")
{
	$nextFamilyInfo = getFamilyInfo($nextFamily * pow(10, 7));
	$nextFamilyLinkText = $nextFamilyInfo["common_name"];
}
else
{
	$nextFamilyLinkText = "";
}

$prevFamily = performCount("Find Previous Family",
    "SELECT FLOOR(MAX(species.id)/POW(10,7)) FROM species, sighting
      WHERE sightings.species_id=species.id
      AND species.id < " . $request->getFamilyID() * pow(10, 7) . " LIMIT 1");

if ($prevFamily != "")
{
	$prevFamilyInfo = getFamilyInfo($prevFamily * pow(10, 7));
	$prevFamilyLinkText = $prevFamilyInfo["common_name"];
}
else
{
	$prevFamilyLinkText = "";
}

htmlHead($familyInfo["common_name"]);
$request->globalMenu();
?>

  <div id="topright-species">
	<? browseButtons("Family Detail", "./familydetail.php?view=".$request->getView()."&familyid=", $request->getFamilyID(),
					 $prevFamily, $prevFamilyLinkText,
					 $nextFamily, $nextFamilyLinkText); ?>

	    <div class="pagetitle"><?= $familyInfo["common_name"] ?></div>
        <div class="pagesubtitle"><?= $familyInfo["latin_name"] ?></div>
<?      $request->viewLinks("species"); ?>

  </div>

    <div id="contentright">
<?
$request->handleStandardViews();
footer();
?>

    </div>

<? htmlFoot(); ?>
