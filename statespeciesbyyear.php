
<?php

require("./birdwalker.php");

$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$stateListCount =  performCount("select count(distinct species.objectid) from species, sighting, location where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.State='" . $abbrev . "'");

$annualStateTotal = performQuery("select count(distinct sighting.SpeciesAbbreviation) as count, year(sighting.TripDate) as year from sighting, location where sighting.LocationName=location.Name and location.State='CA' group by year");
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $stateName ?> State List</title>
  </head>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
$items[] = strtolower($stateName);
navTrailLocations($items);
pageThumbnail("select sighting.*, rand() as shuffle from sighting, location where sighting.Photo='1' and sighting.LocationName=location.Name and location.State='" . $abbrev . "' order by shuffle");
?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle><?php echo $stateName ?></div>
        <div class=pagesubtitle> <?php echo $stateListCount ?> species</div>
      <div class=metadata>
        locations:
        <a href="./statelocations.php?state=<?php echo $abbrev ?>">list</a> |
	    <a href="./statelocationsbyyear.php?state=<?php echo $abbrev ?>">by year</a>
        species:	
        <a href="./statespecies.php?state=<?php echo $abbrev ?>">list</a> |
	    by year
      </div>
      </div>

<?
$gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species, location where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.LocationName=location.Name and location.State='". $abbrev . "' group by sighting.SpeciesAbbreviation order by speciesid";

formatSpeciesByYearTable($gridQueryString, "&state=" . $abbrev, $annualStateTotal);

?>

    </div>
  </body>
</html>
