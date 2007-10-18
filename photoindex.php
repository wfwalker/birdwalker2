<?php

require_once("./birdwalker.php");
require_once("./request.php");

$photoCount = performCount("Count Photos", "SELECT COUNT(*) FROM sighting WHERE Photo='1'");
$photoSpeciesCount = performCount("Count Species with Photos", "SELECT COUNT(DISTINCT(sightings.SpeciesAbbreviation)) FROM sighting WHERE Photo='1'");

htmlHead("Photo List");

$request = new Request;
$request->globalMenu();
?>


  <div id="topright-photo">
	  <div class="pagekind">Index</div>
	  <div class="pagetitle">Photos</div>
      <div class="pagesubtitle">
      <a href="./photoindextaxo.php">by species</a> |
      <a href="./photoindex.php">by date</a> |
      <a href="./photoindexlocation.php">by location</a>
      </div>
  </div>

  <div id="contentright">

  <div class="heading"><?= $photoCount . " photos covering " . $photoSpeciesCount . " species"; ?></div>

<?

$sightingQuery = new SightingQuery($request);
$sightingQuery->formatPhotos(); ?>

footer();
?>

</div>

<?
htmlFoot();
?>
