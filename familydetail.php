
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
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $familyInfo["LatinName"] ?></title>
  </head>

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

<table columns=2>		

<?
$dbQuery = $speciesQuery->performQuery();

while($info = mysql_fetch_array($dbQuery)) {
  $photoQuery = performQuery("select * from sighting where SpeciesAbbreviation='" . $info["Abbreviation"] . "' and Photo='1' order by TripDate desc");
?>

  <tr><td class=report-content>

<?
  if ($photoInfo = mysql_fetch_array($photoQuery)) {
	  echo getThumbForSightingInfo($photoInfo);
  }
?>

  <br><br>
  </td>

  <td class=report-content valign=top>
  <a href="./speciesdetail.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a><br>
  <i><?= $info["LatinName"] ?></i><br><br>
  </td></tr>
<?
}
?>

</table>

    </div>
  </body>
</html>
