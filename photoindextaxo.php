
<?php

require("./birdwalker.php");

$photoSpecies = performQuery("select distinct species.*, count(distinct sighting.objectid) as photoCount from species, sighting where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.Photo='1' group by sighting.SpeciesAbbreviation order by species.objectid");
$photoCount = performCount("select count(*) from sighting where Photo='1'");

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
pageThumbnail("select *, rand() as shuffle from sighting where Photo='1' order by shuffle");
?>

    <div class=contentright>
      <div class="titleblock">	  
	    <div class=pagetitle>Photo Index</div>
        <div class=pagesubtitle><?= $photoCount ?> photos covering <?= mysql_num_rows($photoSpecies) ?> species</div>
        <div class=metadata>by species | <a href="./photoindex.php">by date</a></div>
      </div>

<div class=col1>

<?
$counter = round(mysql_num_rows($photoSpecies)  * 0.6);

while($info = mysql_fetch_array($photoSpecies))
{
	$orderNum =  floor($info["objectid"] / pow(10, 9));
	
	if (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"]))
	{
		$taxoInfo = getBestTaxonomyInfo($info["objectid"]);
?>
        <div class="heading"><?= $taxoInfo["CommonName"] ?></div>
<?
	}

	if ($info["photoCount"] > 1)
	{
?>
        <div class=firstcell><a href="./speciesphotos.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a> (<?= $info["photoCount"] ?>)</div>
<?
	}
	else
	{
?>
        <div class=firstcell><a href="./speciesphotos.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a></div>
<?
	}
		
	$prevInfo = $info;
    $counter--;
    if ($counter == 0) echo "\n</div><div class=col2>";
}
?>

    </div>
  </body>
</html>
