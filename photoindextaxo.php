
<?php

require("./birdwalker.php");

$photoSpecies = performQuery("
    SELECT DISTINCT species.*, COUNT(DISTINCT sighting.objectid) AS photoCount
      FROM species, sighting
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND sighting.Photo='1'
      GROUP BY sighting.SpeciesAbbreviation ORDER BY species.objectid");
$photoCount = performCount("
    SELECT COUNT(*) FROM sighting WHERE Photo='1'");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Photo List</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailPhotos();
?>

    <div class=contentright>
      <div class="titleblock">
<?      rightThumbnailAll(); ?>
	    <div class=pagetitle>Photo Index</div>
        <div class=metadata>
          <a href="./photoindextaxo.php">by species<a/> |
          <a href="./photoindex.php">by date</a> |
          <a href="./photoindexlocation.php">by location</a>
        </div>
      </div>

   <div class=heading><?= $photoCount ?> photos covering <?= mysql_num_rows($photoSpecies) ?> species</div>

<table width="100%">
<tr valign=top><td width="50%" class=report-content>
<?
$counter = round(mysql_num_rows($photoSpecies)  * 0.6);

while($info = mysql_fetch_array($photoSpecies))
{
	$orderNum =  floor($info["objectid"] / pow(10, 9));
	
	if (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"]))
	{
		$taxoInfo = getBestTaxonomyInfo($info["objectid"]); ?>
		<div class="subheading"><?= strtolower($taxoInfo["LatinName"]) ?></div>
<?	}

	if ($info["photoCount"] > 1)
	{ ?>
        <div><a href="./speciesdetailphoto.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a> (<?= $info["photoCount"] ?>)</div>
<?	}
	else
	{ ?>
        <div><a href="./speciesdetailphoto.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a></div>
<?	}
		
	$prevInfo = $info;
    $counter--;
    if ($counter == 0) { ?></td><td width="50%" class=report-content> <? }
} ?>

    </div>
  </body>
</html>
