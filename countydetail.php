
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./sightingquery.php");
require_once("./tripquery.php");

$county = param($_GET, "county", "San Mateo");
$state = param($_GET, "state", "CA");
$view = param($_GET, "view", "species");

$stateName = getStateNameForAbbreviation($state);

?>

<html>

  <? htmlHead($county . " County"); ?>

  <body>

<?php
globalMenu();
disabledBrowseButtons();

$stateInfo = getStateInfoForAbbreviation($state);
$items[]="<a href=\"./statedetail.php?view=" . $view . "&stateid=" . $stateInfo["objectid"] . "\">" . strtolower($stateInfo["Name"]) . "</a>";
navTrailLocations($items);

$locationQuery = new LocationQuery;
$locationQuery->setCounty($county);
$extrema = $locationQuery->findExtrema();

?>

    <div class=contentright>
	  <div class=pagesubtitle>County Detail</div>
      <div class="titleblock">	  
<?    if (($view != "map") && ($view != "photo")) { rightThumbnailCounty($county); } ?>
	  <div class=pagetitle> <?= $county ?> County</div>
      <div class=metadata>
        locations:
        <a href="./countydetail.php?view=locations&state=<?= $state ?>&county=<?= $county ?>">list</a> |
	    <a href="./countydetail.php?view=locationsbymonth&state=<?= $state ?>&county=<?= $county ?>">by month</a> |
	    <a href="./countydetail.php?view=locationsbyyear&state=<?= $state ?>&county=<?= $county ?>">by year</a> |
	    <a href="./countydetail.php?view=map&state=<?= $state ?>&county=<?= $county ?>">map</a> <br/>
        species:	
        <a href="./countydetail.php?view=species&state=<?= $state ?>&county=<?= $county ?>">list</a> |
	    <a href="./countydetail.php?view=speciesbymonth&state=<?= $state ?>&county=<?= $county ?>">by month</a> |
	    <a href="./countydetail.php?view=speciesbyyear&state=<?= $state ?>&county=<?= $county ?>">by year</a> | 
        <a href="./countydetail.php?view=species&view=photo&state=<?= $state ?>&county=<?= $county ?>">photo</a><br/>
	          </div>
      </div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 

	$tripQuery = new TripQuery;
	$tripQuery->setFromRequest($_GET);
	countHeading( $tripQuery->getTripCount(), "trip");
	$tripQuery->formatTwoColumnTripList();
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable(); 
}
elseif ($view == 'speciesbymonth')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatTwoColumnLocationList();

	$tripQuery = new TripQuery;
	$tripQuery->setFromRequest($_GET);
	countHeading( $tripQuery->getTripCount(), "trip");
	$tripQuery->formatTwoColumnTripList();
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
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
else if ($view == "map")
{
	$map = new Map("./countydetail.php");
	$map->setFromRequest($_GET);
	$map->draw();
}
elseif ($view == 'photo')
{
	$sightingQuery = new SightingQuery;
	$sightingQuery->setFromRequest($_GET);
	$sightingQuery->formatPhotos();
}

?>

    </div>
  </body>
</html>