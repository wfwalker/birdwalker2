
<?php

require("./birdwalker.php");

$countyName = $_GET["county"];
$state = $_GET["state"];
$countyCount = performCount("
    SELECT COUNT(DISTINCT species.objectid)
      FROM species, sighting, location
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND sighting.LocationName=location.Name
        AND location.County='" . $countyName . "'");

$monthlyCountyTotal = performQuery("
    SELECT COUNT(DISTINCT sighting.SpeciesAbbreviation) AS count, month(sighting.TripDate) AS month
      FROM sighting, location
      WHERE sighting.LocationName=location.Name AND location.County='" . $countyName . "' AND location.State='" . $state . "'
      GROUP BY month");
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
navTrailCounty($state, $countyName);
?>

    <div class=contentright>
	  <div class="titleblock">
<?      rightThumbnailCounty($countyName);?>
	  <div class=pagetitle><?= $countyName ?> County</div>
      <div class=metadata>
<?     countyViewLinks($state, $countyName); ?>
      </div>
    </div>

 <div class=heading><?= $countyCount ?> species</div>


<? formatSpeciesByMonthTable(
    "WHERE sighting.SpeciesAbbreviation=species.Abbreviation
        AND sighting.LocationName=location.Name AND location.County='". $countyName . "'",
        "&county=" . $countyName, $monthlyCountyTotal); ?>

    </div>
  </body>
</html>
