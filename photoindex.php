
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

<?php globalMenu(); disabledBrowseButtons(); ?>

<div class=navigationright><a href="./index.php">birdWalker</a> &gt; <a href="./photoindex.php">photo</a> &gt; <a href="./photoindextaxo.php">by species</a> | by date</div>

    <div class=contentright>
      <div class="titleblock">	  
	    <div class=pagetitle>Photo Index</div>
        <div class=pagesubtitle><?php echo $photoCount . " photos covering " . $photoSpeciesCount . " species"; ?></div>
      </div>

<table cellpadding=4 columns=2>
<?
$photoQuery = performQuery("select concat(sighting.TripDate, sighting.objectid) as photoOrder, sighting.*, species.CommonName, date_format(sighting.TripDate, '%M %e, %Y') as niceDate from sighting, species where Photo='1' and sighting.SpeciesAbbreviation=species.Abbreviation order by photoOrder desc");


$counter = 0;

while($info = mysql_fetch_array($photoQuery)) {
	if (($counter % 2) == 0) echo "\n<tr>";
	echo "<td>" . getThumbForSightingInfo($info) . "</td>";
	echo "<td class=report-content valign=top>" . $info["CommonName"] . "<br/>";
	echo $info["niceDate"] . "<br/>";
	echo $info["LocationName"] .  "<br/>";
	echo $info[""] . "</td>";
	if (($counter % 2) == 1) echo "</tr>";
	$counter++;
}

?>

    </div>
  </body>
</html>
