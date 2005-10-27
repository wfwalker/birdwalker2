
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$photoCount = performCount("Count Photos", "SELECT COUNT(*) FROM sighting WHERE Photo='1'");
$photoSpeciesCount = performCount("Count Species with Photos", "SELECT COUNT(DISTINCT(sighting.SpeciesAbbreviation)) FROM sighting WHERE Photo='1'");

htmlHead("Photo List");

$request = new Request;
$request->globalMenu();
?>


  <div class="topright">
	  <div class="pagesubtitle">Index</div>
	  <div class="pagetitle">Photos</div>
  </div>

  <div class=contentright>
    <div class="titleblock">	  
      <div class="viewlinks">
        <a href="./photoindextaxo.php">by species</a> |
        <a href="./photoindex.php">by date</a> |
        <a href="./photoindexlocation.php">by location</a>
      </div>
    </div>

  <div class=heading><?= $photoCount . " photos covering " . $photoSpeciesCount . " species"; ?></div>

<?

$sightingQuery = new SightingQuery($request);
$sightingQuery->formatPhotos(); ?>

footer();
?>

</div>

<?
htmlFoot();
?>