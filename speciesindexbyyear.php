
<?php

require("./birdwalker.php");

$lifeCount = performCount("
    SELECT count(distinct species.objectid)
      FROM species, sighting
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
      AND sighting.Exclude!='1'");

$annualTotal = performQuery("
    SELECT count(distinct sighting.SpeciesAbbreviation) as count, year(sighting.TripDate) as year
      FROM sighting
      GROUP BY year");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Species</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailBirds();
pageThumbnail("SELECT *, rand() AS shuffle FROM sighting WHERE Photo='1' ORDER BY shuffle");
?>


    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Species</div>
        <div class=pagesubtitle><?= $lifeCount ?> Species</div>
	  <div class=metadata><a href="./speciesindex.php">list</a> | by year</div>
      </div>

<?
$gridQueryString="
    SELECT distinct(CommonName), species.objectid AS speciesid, bit_or(1 << (year(TripDate) - 1995)) AS mask
      FROM sighting, species
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation and sighting.Exclude=0
      GROUP BY sighting.SpeciesAbbreviation
      ORDER BY speciesid";

formatSpeciesByYearTable($gridQueryString, "", $annualTotal);

?>

    </div>
  </body>
</html>
