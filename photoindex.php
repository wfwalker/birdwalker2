
<?php

require("./birdwalker.php");

$photoCount = performCount("select count(*) from sighting where Photo='1'");
$photoSpeciesCount = performCount("select count(distinct(sighting.SpeciesAbbreviation)) from sighting where Photo='1'");

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
	    <div class=pagetitle>Photo Index</div>
        <div class=metadata><a href="./photoindextaxo.php">by species</a> | by date</div>
      </div>

<div class=heading><?= $photoCount . " photos covering " . $photoSpeciesCount . " species"; ?></div>

<table cellpadding=4 columns=2>

<?php
$photoQuery = performQuery("select concat(sighting.TripDate, sighting.objectid) as photoOrder, sighting.*, species.CommonName, date_format(sighting.TripDate, '%M %e, %Y') as niceDate from sighting, species where Photo='1' and sighting.SpeciesAbbreviation=species.Abbreviation order by photoOrder desc");


$counter = 0;

while($info = mysql_fetch_array($photoQuery))
{
	if (($counter % 2) == 0)
	{
?>      <tr> <?
    }
?>
    <td valign=top align=right><?= getThumbForSightingInfo($info) ?></td>
    <td class=report-content valign=top>
	    <?= $info["CommonName"] ?><br/>
        <?= $info["niceDate"] ?><br/>
        <?= $info["LocationName"] ?><br/>
        <?= $info[""] ?></td>
<?
    if (($counter % 4) == 1)
	{
?>      </tr> <?
    }

    $counter++;
}
?>

    </div>
  </body>
</html>
