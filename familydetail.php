
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
    "SELECT FLOOR(MIN(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid >" . ($request->getFamilyID() + 1) * pow(10, 7) . " LIMIT 1");

if ($nextFamily != "")
{
	$nextFamilyInfo = getFamilyInfo($nextFamily * pow(10, 7));
	$nextFamilyLinkText = $nextFamilyInfo["LatinName"];
}
else
{
	$nextFamilyLinkText = "";
}

$prevFamily = performCount("Find Previous Family",
    "SELECT FLOOR(MAX(species.objectid)/POW(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid < " . $request->getFamilyID() * pow(10, 7) . " LIMIT 1");

if ($prevFamily != "")
{
	$prevFamilyInfo = getFamilyInfo($prevFamily * pow(10, 7));
	$prevFamilyLinkText = $prevFamilyInfo["LatinName"];
}
else
{
	$prevFamilyLinkText = "";
}

htmlHead($familyInfo["LatinName"]);
$request->globalMenu();
?>

  <div class="topright">
	<? browseButtons("<img align=\"center\" src=\"./images/species.gif\"> Family Detail", "./familydetail.php?view=".$request->getView()."&familyid=", $request->getFamilyID(),
					 $prevFamily, $prevFamilyLinkText,
					 $nextFamily, $nextFamilyLinkText); ?>

	    <div class=pagetitle><?= $familyInfo["LatinName"] ?></div>
        <div class=pagesubtitle><?= $familyInfo["CommonName"] ?></div>
  </div>

    <div class="contentright">
	  <div class="titleblock">
 
<?      $request->viewLinks("species"); ?>

      </div>


<?
$request->handleStandardViews();
footer();
?>

    </div>

<? htmlFoot(); ?>
