
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./tripquery.php");
require_once("./locationquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$request = new Request;

$familyInfo = $request->getFamilyInfo();
$orderInfo = $request->getOrderInfo();

$firstFamily = performCount("
    SELECT FLOOR(MIN(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation LIMIT 1");
$lastFamily = performCount("
    SELECT FLOOR(MAX(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation LIMIT 1");
$nextFamily = performCount("
    SELECT FLOOR(MIN(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid>" . ($request->getFamilyID() + 1) * pow(10, 7) . " LIMIT 1");
$prevFamily = performCount("
    SELECT FLOOR(MAX(species.objectid)/POW(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid<" . ($request->getFamilyID() - 1) * pow(10, 7) . " LIMIT 1");

htmlHead($familyInfo["LatinName"]);

globalMenu();
$items[] = "<a href=\"./orderdetail.php?view=" . $request->getView() . "&orderid=" . round($orderInfo["objectid"] / pow(10, 9)) . "\">" . strtolower($orderInfo["LatinName"]) . "</a>";
navTrailBirds($items);
 ?>

    <div class=contentright>
	<? browseButtons("Family Detail", "./familydetail.php?view=".$request->getView()."&familyid=", $request->getFamilyID(), $firstFamily, $prevFamily, $nextFamily, $lastFamily); ?>

	  <div class="titleblock">
	    <div class=pagetitle><?= $familyInfo["CommonName"] ?></div>
        <div class=metadata><?= $familyInfo["LatinName"] ?></div>
        <div class=metadata>
          locations:
            <a href="./familydetail.php?view=locations&familyid=<?= $request->getFamilyID() ?>">list</a> |
            <a href="./familydetail.php?view=locationsbymonth&familyid=<?= $request->getFamilyID() ?>">by month</a> |
	        <a href="./familydetail.php?view=locationsbyyear&familyid=<?= $request->getFamilyID() ?>">by year</a> |
	        <a href="./familydetail.php?view=map&familyid=<?= $request->getFamilyID() ?>">map</a> <br/>
          species:	
            <a href="./familydetail.php?view=species&familyid=<?= $request->getFamilyID() ?>">list</a> |
	        <a href="./familydetail.php?view=chrono&familyid=<?= $request->getFamilyID() ?>">ABA</a> |
	        <a href="./familydetail.php?view=speciesbymonth&familyid=<?= $request->getFamilyID() ?>">by month</a> |
	        <a href="./familydetail.php?view=speciesbyyear&familyid=<?= $request->getFamilyID() ?>">by year</a><br/>
	    </div>
      </div>


<?
$request->handleStandardViews("species");
footer();
?>

    </div>

<? htmlFoot(); ?>
