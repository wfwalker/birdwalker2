
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./locationquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$view = param($_GET, "view", "species");

$orderid = reqParam($_GET, "orderid");

$speciesQuery = new SpeciesQuery;
$speciesQuery->setOrder($orderid);

$orderInfo = getOrderInfo($orderid * pow(10, 9));

htmlHead($orderInfo["LatinName"]);

globalMenu();
$items[] = strtolower($orderInfo["LatinName"]);
navTrailBirds($items);
?>

    <div class=contentright>
	<? browseButtons("Order Detail", "./orderdetail.php?view=" . $view . "&orderid=", $orderid, 1, $orderid - 1, $orderid + 1, 22); ?>

	  <div class="titleblock">
        <div class=pagetitle><?= $orderInfo["CommonName"] ?></div>
        <div class=metadata> <?= $orderInfo["LatinName"] ?></div>
        <div class=metadata>
          locations:
            <a href="./orderdetail.php?view=locations&orderid=<?= $orderid ?>">list</a> |
            <a href="./orderdetail.php?view=locationsbymonth&orderid=<?= $orderid ?>">by month</a> |
	        <a href="./orderdetail.php?view=locationsbyyear&orderid=<?= $orderid ?>">by year</a> |
	        <a href="./orderdetail.php?view=map&orderid=<?= $orderid ?>">map</a> <br/>
          species:	
            <a href="./orderdetail.php?view=species&orderid=<?= $orderid ?>">list</a> |
	        <a href="./orderdetail.php?view=chrono&orderid=<?= $orderid ?>">ABA</a> |
	        <a href="./orderdetail.php?view=speciesbymonth&orderid=<?= $orderid ?>">by month</a> |
	        <a href="./orderdetail.php?view=speciesbyyear&orderid=<?= $orderid ?>">by year</a><br/>
	    </div>
      </div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setOrder($orderid);
	countHeading($speciesQuery->getSpeciesCount(), "species");
    formatSpeciesListWithPhoto($speciesQuery);
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setOrder($orderid);
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
	$locationQuery->formatTwoColumnLocationList(true);
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
	$locationQuery->setFromRequest($_GET);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByMonthTable();
}
else if ($view == "map")
{
	$map = new Map("./orderdetail.php");
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