
<?php

require("./birdwalker.php");

$countyName = $_GET["county"];
$whereClause =  "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.County='" . $countyName . "'";
$countyListCount = getFancySpeciesCount($whereClause);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $countyName ?> County List</title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle><?php echo $countyName ?> County List</div>
        <div class=pagesubtitle> <?php echo $countyListCount ?> species</div>
      </div>

<?
$gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species, location where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.LocationName=location.Name and location.County='". $countyName . "' group by sighting.SpeciesAbbreviation order by speciesid";

formatSpeciesByYearTable($countyListCount, $gridQueryString);

?>

    </div>
  </body>
</html>
