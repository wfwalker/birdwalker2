
<?php

require("./birdwalker.php");

$countyName = $_GET["county"];
$state = $_GET["state"];
$countyCount = performCount("select count(distinct species.objectid) from species, sighting, location where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.County='" . $countyName . "'");

$monthlyCountyTotal = performQuery("select count(distinct sighting.SpeciesAbbreviation) as count, month(sighting.TripDate) as month from sighting, location where sighting.LocationName=location.Name and location.County='" . $countyName . "' and location.State='" . $state . "' group by month");

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
<?        countyViewLinks($state, $countyName); ?>
      </div>

      </div>

 <div class=heading><?= $countyCount ?> species</div>


<?
$gridQueryString="
    SELECT DISTINCT(CommonName), species.objectid AS speciesid, bit_or(1 << month(TripDate)) AS mask
      FROM sighting, species, location
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
        AND sighting.LocationName=location.Name AND location.County='". $countyName . "'
      GROUP BY sighting.SpeciesAbbreviation
      ORDER BY speciesid";

formatSpeciesByMonthTable($gridQueryString, "&county=" . $countyName, $monthlyCountyTotal);

?>

    </div>
  </body>
</html>
