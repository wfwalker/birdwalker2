
<?php

require("./birdwalker.php");
require("./speciesquery.php");

$familyid = param($_GET, "family", 701);
$orderid = floor($familyid / 100);

$familyInfo = getFamilyInfo($familyid * pow(10, 7));
$orderInfo = getOrderInfo($orderid * pow(10, 9));

$speciesQuery = new SpeciesQuery;
$speciesQuery->setFamily($familyid);

$firstFamily = performCount("
    SELECT FLOOR(MIN(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation LIMIT 1");
$lastFamily = performCount("
    SELECT FLOOR(MAX(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation LIMIT 1");
$nextFamily = performCount("
    SELECT FLOOR(MIN(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid>" . ($familyid + 1) * pow(10, 7) . " LIMIT 1");
$prevFamily = performCount("
    SELECT FLOOR(MAX(species.objectid)/POW(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid<" . ($familyid - 1) * pow(10, 7) . " LIMIT 1");
?>

<html>

  <? htmlHead($familyInfo["LatinName"]); ?>

  <body>

<?php
globalMenu();
browseButtons("./familydetail.php?family=", $familyid, $firstFamily, $prevFamily, $nextFamily, $lastFamily);
$items[] = "<a href=\"./orderdetail.php?order=" . $orderInfo["objectid"] / pow(10, 9) . "\">" . strtolower($orderInfo["LatinName"]) . "</a>";
navTrailBirds($items);
 ?>

    <div class=contentright>
      <div class=pagesubtitle><?= $familyInfo["LatinName"] ?></div>
	  <div class="titleblock">
	    <div class=pagetitle><?= $familyInfo["CommonName"] ?></div>
      </div>


<div class=heading><?= $speciesQuery->getSpeciesCount() ?> species</div>

	<? formatSpeciesListWithPhoto($speciesQuery); ?>

    </div>
  </body>
</html>
