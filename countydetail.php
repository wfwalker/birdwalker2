
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./chronolist.php");
require_once("./sightingquery.php");
require_once("./tripquery.php");

$county = reqParam($_GET, "county");
$stateid = reqParam($_GET, "stateid");
$view = param($_GET, "view", "species");

htmlHead($county . " County");

globalMenu();

$stateInfo = getStateInfo($stateid);
$stateName = $stateInfo["Name"];

$items[]="<a href=\"./statedetail.php?view=" . $view . "&stateid=" . $stateInfo["objectid"] . "\">" . strtolower($stateInfo["Name"]) . "</a>";
navTrailLocations($items);

$locationQuery = new LocationQuery;
$locationQuery->setFromRequest($_GET);
$extrema = $locationQuery->findExtrema();

?>

    <div class=contentright>
	<? disabledBrowseButtons("County Detail"); ?>
      <div class="titleblock">	  
<?    if (($view != "map") && ($view != "photo")) { rightThumbnailCounty($county); } ?>
	  <div class=pagetitle> <?= $county ?> County</div>
      <div class=metadata>
        locations:
        <a href="./countydetail.php?view=locations&stateid=<?= $stateid ?>&county=<?= $county ?>">list</a> |
	    <a href="./countydetail.php?view=locationsbymonth&stateid=<?= $stateid ?>&county=<?= $county ?>">by month</a> |
	    <a href="./countydetail.php?view=locationsbyyear&stateid=<?= $stateid ?>&county=<?= $county ?>">by year</a> |
	    <a href="./countydetail.php?view=map&stateid=<?= $stateid ?>&county=<?= $county ?>">map</a> <br/>
        species:	
        <a href="./countydetail.php?view=species&stateid=<?= $stateid ?>&county=<?= $county ?>">list</a> |
	    <a href="./countydetail.php?view=chrono&stateid=<?= $stateid ?>&county=<?= $county ?>">ABA</a> |
	    <a href="./countydetail.php?view=speciesbymonth&stateid=<?= $stateid ?>&county=<?= $county ?>">by month</a> |
	    <a href="./countydetail.php?view=speciesbyyear&stateid=<?= $stateid ?>&county=<?= $county ?>">by year</a> | 
        <a href="./countydetail.php?view=species&view=photo&stateid=<?= $stateid ?>&county=<?= $county ?>">photo</a><br/>
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
	$locationQuery->formatTwoColumnLocationList(false);

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
else if ($view == "chrono")
{
	$chrono = new ChronoList;
	$chrono->setFromRequest($_GET);
	$chrono->draw();
}
elseif ($view == 'photo')
{
	$sightingQuery = new SightingQuery;
	$sightingQuery->setFromRequest($_GET);
	$sightingQuery->formatPhotos();
}


footer();
?>

    </div>

<?
htmlFoot();
?>
