
<?php

require("./birdwalker.php");

$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$stateListCount =  performCount("select count(distinct species.objectid) from species, sighting, location where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.State='" . $abbrev . "'");
$randomPhotoSightings = performQuery("select sighting.*, rand() as shuffle from sighting, location where sighting.Photo='1' and sighting.LocationName=location.Name and location.State='" . $abbrev . "' order by shuffle");

// select count(distinct sighting.SpeciesAbbreviation) as thecount, year(sighting.TripDate) as theyear from sighting, location where sighting.LocationName=location.Name and location.State='CA' group by theyear;
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $stateName ?> State List</title>
  </head>

  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailBirds(); ?>

<div class=thumb><?php  if (mysql_num_rows($randomPhotoSightings) > 0) { $photoInfo = mysql_fetch_array($randomPhotoSightings); if (mysql_num_rows($randomPhotoSightings) > 0) echo "<td>" . getThumbForSightingInfo($photoInfo) . "</td>"; } ?></div>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle><?php echo $stateName ?></div>
        <div class=pagesubtitle> <?php echo $stateListCount ?> species</div>
      </div>

<?
$gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species, location where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.LocationName=location.Name and location.State='". $abbrev . "' group by sighting.SpeciesAbbreviation order by speciesid";

formatSpeciesByYearTable($gridQueryString, "&state=" . $abbrev);

?>

    </div>
  </body>
</html>
