
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
?>


    <div class=contentright>
      <div class="titleblock">
<?      rightThumbnailAll(); ?>
        <div class=pagetitle>Species</div>
        <div class=metadata><a href="./speciesindex.php">list</a> | by month | <a href="./speciesindexbyyear.php">by year</a></div>
      </div>

 <div class=heading><?= $lifeCount ?> Species</div>

<? formatSpeciesByMonthTable("
    WHERE sighting.SpeciesAbbreviation=species.Abbreviation AND sighting.LocationName=location.Name",
      "", $annualTotal); ?>

    </div>
  </body>
</html>
