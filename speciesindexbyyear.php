
<?php

require("./birdwalker.php");

$lifeCount = performCount("select count(distinct species.objectid) from species, sighting where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.Exclude!='1'");

$annualTotal = performQuery("select count(distinct sighting.SpeciesAbbreviation) as count, year(sighting.TripDate) as year from sighting group by year");

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
pageThumbnail("select *, rand() as shuffle from sighting where Photo='1' order by shuffle");
?>


    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Species</div>
        <div class=pagesubtitle><?= $lifeCount ?> Species</div>
	  <div class=metadata><a href="./speciesindex.php">list</a> | by year</div>
      </div>

<?
$gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.Exclude=0 group by sighting.SpeciesAbbreviation order by speciesid";

formatSpeciesByYearTable($gridQueryString, "", $annualTotal);

?>

    </div>
  </body>
</html>
