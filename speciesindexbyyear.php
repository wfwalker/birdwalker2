
<?php

require("./birdwalker.php");

$lifeCount = performCount("
    SELECT count(distinct species.objectid)
      FROM species, sighting
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation");

$annualTotal = performQuery("
    SELECT count(distinct sighting.SpeciesAbbreviation) as count, year(sighting.TripDate) as year
      FROM sighting, species
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
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
?>


    <div class=contentright>
      <div class="titleblock">	  
<?      rightThumbnailAll(); ?>
        <div class=pagetitle>Species</div>
	  <div class=metadata><a href="./speciesindex.php">list</a> | <a href="./speciesindexbymonth.php">by month</a> | by year</div>
      </div>

     <div class=heading><?= $lifeCount ?> Species</div>

<? formatSpeciesByYearTable("
    WHERE sighting.SpeciesAbbreviation=species.Abbreviation and sighting.Exclude!='1'
      AND sighting.LocationName=location.Name", "", $annualTotal); ?>

    </div>
  </body>
</html>
