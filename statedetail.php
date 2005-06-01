
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$id = reqParam($_GET, "stateid");
$view = param($_GET, "view", "species");

$request = new Request;

$info = getStateInfo($id);

htmlHead($info["Name"]);

globalMenu();
navTrailLocations($view);

$locationQuery = new LocationQuery($request);
$extrema = $locationQuery->findExtrema();

?>

    <div class=contentright>
      <? stateBrowseButtons($id, $view); ?>
      <div class="titleblock">	  
<?    if ($view != "map") rightThumbnailState($info["Abbreviation"]); ?>
	  <div class=pagetitle><?= $info["Name"] ?></div>
      <div class=metadata>
        locations:
        <a href="./statedetail.php?view=locations&stateid=<?= $id ?>">list</a> |
	    <a href="./statedetail.php?view=locationsbymonth&stateid=<?= $id ?>">by month</a> |
	    <a href="./statedetail.php?view=locationsbyyear&stateid=<?= $id ?>">by year</a> |
	    <a href="./statedetail.php?view=map&stateid=<?= $id ?>">map</a><br/>
        species:	
        <a href="./statedetail.php?view=species&stateid=<?= $id ?>">list</a> |
	    <a href="./statedetail.php?view=chrono&stateid=<?= $id ?>">ABA</a> |
	    <a href="./statedetail.php?view=speciesbymonth&stateid=<?= $id ?>">by month</a> |
	    <a href="./statedetail.php?view=speciesbyyear&stateid=<?= $id ?>">by year</a> | 
        <a href="./statedetail.php?view=species&view=photo&stateid=<?= $id ?>">photo</a><br/>
      </div>
      </div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery($request);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery($request);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable(); 
}
elseif ($view == 'speciesbymonth')
{
	$speciesQuery = new SpeciesQuery($request);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery($request);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatTwoColumnLocationList($view, true);
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery($request);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByYearTable();
}
elseif ($view == 'locationsbymonth')
{
    $locationQuery = new LocationQuery($request);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByMonthTable();
}
else if ($view == "map")
{
	$map = new Map("./statedetail.php", $request);
	$map->draw();
}
else if ($view == "chrono")
{
	$chrono = new ChronoList($request);
	$chrono->draw();
}
elseif ($view == 'photo')
{
	$sightingQuery = new SightingQuery($request);
	$sightingQuery->formatPhotos();
}

footer();

?>

    </div>

<?
htmlFoot();
?>
