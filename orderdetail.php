
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./locationquery.php");
require_once("./map.php");

$view = param($_GET, "view", "species");

$orderid = param($_GET, "order", 21);

$speciesQuery = new SpeciesQuery;
$speciesQuery->setOrder($orderid);

$orderInfo = getOrderInfo($orderid * pow(10, 9));

htmlHead($orderInfo["LatinName"]);

globalMenu();
disabledBrowseButtons();
browseButtons("./orderdetail.php?view=" . $view . "&order=", $orderid, 1, $orderid - 1, $orderid + 1, $orderCount);
$items[] = strtolower($orderInfo["LatinName"]);
navTrailBirds($items);
?>

    <div class=contentright>
      <div class=pagesubtitle>Order Detail</div>
	  <div class="titleblock">
        <div class=pagetitle><?= $orderInfo["CommonName"] ?></div>
        <div class=metadata> <?= $orderInfo["LatinName"] ?></div>
        <div class=metadata>
          locations:
            <a href="./orderdetail.php?view=locations&state=<?= $state ?>&order=<?= $orderid ?>">list</a> |
            <a href="./orderdetail.php?view=locationsbymonth&state=<?= $state ?>&order=<?= $orderid ?>">by month</a> |
	        <a href="./orderdetail.php?view=locationsbyyear&state=<?= $state ?>&order=<?= $orderid ?>">by year</a> |
	        <a href="./orderdetail.php?view=map&state=<?= $state ?>&order=<?= $orderid ?>">map</a> <br/>
          species:	
            <a href="./orderdetail.php?view=species&state=<?= $state ?>&order=<?= $orderid ?>">list</a> |
	        <a href="./orderdetail.php?view=speciesbymonth&state=<?= $state ?>&order=<?= $orderid ?>">by month</a> |
	        <a href="./orderdetail.php?view=speciesbyyear&state=<?= $state ?>&order=<?= $orderid ?>">by year</a><br/>
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

footer();

?>

    </div>

<?
htmlFoot();
?>