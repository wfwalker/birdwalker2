
<?php

require("./birdwalker.php");
require("./speciesquery.php");

$aQuery = new SpeciesQuery;

$aQuery->setLocationID($_GET["locationid"]);
$aQuery->setYear($_GET["year"]);
$aQuery->setMonth($_GET["month"]);
$aQuery->setCounty($_GET["county"]);
$aQuery->setState($_GET["state"]);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $pageTitle ?></title>
  </head>
  <body>

<?
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

    <div class=contentright>
      <div class="titleblock">
<?    $aQuery->rightThumbnail() ?>
      <div class=pagetitle><?= $aQuery->getPageTitle() ?></div>
<?    if (($state == 'CA') && ($year != "")) { ?><div class=metadata>See also our <a href="./chronocayearlist.php?year=<?=$year?>">California ABA Year List for <?=$year?></a></div><? } ?>
      </div>

	  <div class=heading><?= $aQuery->getSpeciesCount() ?> Species</div>

<? $aQuery->formatTwoColumnSpeciesList(); ?>

    </div>
  </body>
</html>