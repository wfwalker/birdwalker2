
<?php

require("./birdwalker.php");
require("./speciesquery.php");
require("./sightingquery.php");
require("./locationquery.php");

$county = param($_GET, "county", "San Mateo");
$state = param($_GET, "state", "CA");
$view = param($_GET, "view", "species");

$stateName = getStateNameForAbbreviation($state);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $county ?> County, <?= $stateName ?></title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailCounty($state, $county);

?>

    <div class=contentright>
      <div class="titleblock">	  
<?    if ($view != "photo") { rightThumbnailCounty($county); } ?>
	  <div class=pagetitle> <?= $county ?> County</div>
      <div class=metadata>
        locations:
        <a href="./countydetail.php?view=locations&state=<?= $state ?>&county=<?= $county ?>">list</a> |
	    <a href="./countydetail.php?view=locationsbymonth&state=<?= $state ?>&county=<?= $county ?>">by month</a> |
	    <a href="./countydetail.php?view=locationsbyyear&state=<?= $state ?>&county=<?= $county ?>">by year</a><br/>
        species:	
        <a href="./countydetail.php?view=species&state=<?= $state ?>&county=<?= $county ?>">list</a> |
	    <a href="./countydetail.php?view=speciesbymonth&state=<?= $state ?>&county=<?= $county ?>">by month</a> |
	    <a href="./countydetail.php?view=speciesbyyear&state=<?= $state ?>&county=<?= $county ?>">by year</a><br/>
        <a href="./countydetail.php?view=species&view=photo&state=<?= $state ?>&county=<?= $county ?>">photo</a>
      </div>
      </div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setCounty($county);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setCounty($county);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable(); 
}
elseif ($view == 'speciesbymonth')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setCounty($county);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setCounty($county);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatTwoColumnLocationList();
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setCounty($county);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByYearTable();
}
elseif ($view == 'locationsbymonth')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setCounty($county);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByMonthTable();
}
elseif ($view == 'photo')
{
	$sightingQuery = new SightingQuery;
	$sightingQuery->setCounty($county);
	$sightingQuery->formatPhotos();
}

?>

    </div>
  </body>
</html>