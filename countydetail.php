
<?php

require("./birdwalker.php");

$countyName = $_GET["county"];
$countyCount = performCount("select count(distinct species.objectid) from species, sighting, location where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.County='" . $countyName . "'");
$randomPhotoSightings = performQuery("select sighting.*, rand() as shuffle from sighting, location where sighting.Photo='1' and sighting.LocationName=location.Name and location.County='" . $countyName . "' order by shuffle");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $countyName ?> County List</title>
  </head>

  <body>

<?php globalMenu(); disabledBrowseButtons() ?>

<div class=thumb><?php  if (mysql_num_rows($randomPhotoSightings) > 0) { $photoInfo = mysql_fetch_array($randomPhotoSightings); if (mysql_num_rows($randomPhotoSightings) > 0) echo "<td>" . getThumbForSightingInfo($photoInfo) . "</td>"; } ?></div>

<div class=navigationright><a href="./index.php">birdWalker</a> &gt; <a href="./locationindex.php">locations</a> &gt; county by year</div>

    <div class=contentright>
	  <div class="titleblock">
	  <div class=pagetitle><?php echo $countyName ?> County</div>
        <div class=pagesubtitle> <?php echo $countyCount ?> species</div>
      </div>

<?
$gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species, location where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.LocationName=location.Name and location.County='". $countyName . "' group by sighting.SpeciesAbbreviation order by speciesid";

formatSpeciesByYearTable($gridQueryString, "&county=" . $countyName);

?>

    </div>
  </body>
</html>
