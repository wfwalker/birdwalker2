
<?php

require("./birdwalker.php");

$lifeCount = performCount("
    SELECT count(distinct species.objectid)
      FROM species, sighting
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation");

$annualTotal = performQuery("
    SELECT count(distinct sighting.SpeciesAbbreviation) as count, month(sighting.TripDate) as month
      FROM sighting, species
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
      GROUP BY month");

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
	  <div class=metadata><a href="./speciesindex.php">list</a> | by month | <a href="./speciesindexbyyear.php">by year</a></div>
      </div>

<?
$gridQueryString="
    SELECT distinct(CommonName), species.objectid AS speciesid, bit_or(1 << month(TripDate)) AS mask
      FROM sighting, species
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      GROUP BY sighting.SpeciesAbbreviation
      ORDER BY speciesid";

formatSpeciesByMonthTable($gridQueryString, "", $annualTotal);

?>

    </div>
  </body>
</html>
