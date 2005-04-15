
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$id = reqParam($_GET, "stateid");
$view = param($_GET, "view", "species");

$info = getStateInfo($id);

htmlHead($info["Name"]);

globalMenu();
stateBrowseButtons($id, $view);
navTrailLocations();

$locationQuery = new LocationQuery;
$locationQuery->setFromRequest($_GET);
$extrema = $locationQuery->findExtrema();

?>

    <div class=contentright>
      <div class="pagesubtitle">State Detail</div>
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
	    <a href="./statedetail.php?view=speciesbyyear&stateid=<?= $id ?>">by year</a><br/>
      </div>
      </div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable(); 
}
elseif ($view == 'speciesbymonth')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatTwoColumnLocationList(true);
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByYearTable();
}
elseif ($view == 'locationsbymonth')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByMonthTable();
}
else if ($view == "map")
{
	$map = new Map("./statedetail.php");
	$map->setFromRequest($_GET);
	$map->draw();
}
else if ($view == "chrono")
{
	$chrono = new ChronoList;
	$chrono->setFromRequest($_GET);
	$chrono->draw();
}

footer();

?>

    </div>

<?
htmlFoot();
?>
