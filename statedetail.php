
<?php

require("./birdwalker.php");

$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$whereClause =  "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.State='" . $abbrev . "'";
$stateListCount = getFancySpeciesCount($whereClause);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $stateName ?> State List</title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle><?php echo $stateName ?> State List</div>
        <div class=pagesubtitle> <?php echo $stateListCount ?> species</div>
      </div>

<?
$gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species, location where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.LocationName=location.Name and location.State='". $abbrev . "' group by sighting.SpeciesAbbreviation order by speciesid";

formatSpeciesByYearTable($stateListCount, $gridQueryString);

?>

    </div>
  </body>
</html>
