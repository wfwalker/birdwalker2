
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$photoCount = performCount("select count(*) from sighting where Photo='1'");
$photoSpeciesCount = performCount("select count(distinct(sighting.SpeciesAbbreviation)) from sighting where Photo='1'");

htmlHead("Photo List");

$request = new Request;
$request->globalMenu();
?>


  <div class=contentright>
	<div class="pagesubtitle">Index</div>

    <div class="titleblock">	  
	  <div class="pagetitle">Photos</div>
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