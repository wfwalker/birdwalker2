
<?php

require("./birdwalker.php");

$countyName = $_GET["county"];
$state = $_GET["state"];
$countyCount = performCount("select count(distinct species.objectid) from species, sighting, location where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.County='" . $countyName . "'");

$annualCountyTotal = performQuery("select count(distinct sighting.SpeciesAbbreviation) as count, year(sighting.TripDate) as year from sighting, location where sighting.LocationName=location.Name and location.County='" . $countyName . "' and location.State='" . $state . "' group by year");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $countyName ?> County</title>
  </head>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
$items[] = "<a href=\"./statespeciesbyyear.php?state=" .  $state . "\">" . strtolower(getStateNameForAbbreviation($state)) . "</a>";
$items[] = strtolower($countyName) . " county";
navTrailLocations($items);
pageThumbnail("select sighting.*, rand() as shuffle from sighting, location where sighting.Photo='1' and sighting.LocationName=location.Name and location.County='" . $countyName . "' order by shuffle");
?>

    <div class=contentright>
	  <div class="titleblock">
	  <div class=pagetitle><?= $countyName ?> County</div>
        <div class=pagesubtitle><?= $countyCount ?> species</div>

      <div class=metadata>
        locations:
        <a href="./countylocations.php?state=<?= $state ?>&county=<?= urlencode($countyName) ?>">list</a> |
	    <a href="./countylocationsbyyear.php?state=<?= $state ?>&county=<?= urlencode($countyName) ?>">by year</a>
        species:	
        <a href="./countyspecies.php?state=<?= $state ?>&county=<?= urlencode($countyName) ?>">list</a> |
	    by year
      </div>

      </div>

<?
$gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species, location where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.LocationName=location.Name and location.County='". $countyName . "' group by sighting.SpeciesAbbreviation order by speciesid";

formatSpeciesByYearTable($gridQueryString, "&county=" . $countyName, $annualCountyTotal);

?>

    </div>
  </body>
</html>
