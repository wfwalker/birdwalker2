
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./tripquery.php");
require_once("./locationquery.php");
require_once("./map.php");

$familyid = param($_GET, "family", 701);
$orderid = floor($familyid / 100);
$view = param($_GET, "view", "species");

$familyInfo = getFamilyInfo($familyid * pow(10, 7));
$orderInfo = getOrderInfo($orderid * pow(10, 9));

$firstFamily = performCount("
    SELECT FLOOR(MIN(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation LIMIT 1");
$lastFamily = performCount("
    SELECT FLOOR(MAX(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation LIMIT 1");
$nextFamily = performCount("
    SELECT FLOOR(MIN(species.objectid)/pow(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid>" . ($familyid + 1) * pow(10, 7) . " LIMIT 1");
$prevFamily = performCount("
    SELECT FLOOR(MAX(species.objectid)/POW(10,7)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid<" . ($familyid - 1) * pow(10, 7) . " LIMIT 1");

htmlHead($familyInfo["LatinName"]);

globalMenu();
browseButtons("./familydetail.php?view=".$view."&family=", $familyid, $firstFamily, $prevFamily, $nextFamily, $lastFamily);
$items[] = "<a href=\"./orderdetail.php?order=" . $orderInfo["objectid"] / pow(10, 9) . "\">" . strtolower($orderInfo["LatinName"]) . "</a>";
navTrailBirds($items);
 ?>

    <div class=contentright>
      <div class=pagesubtitle>Family Detail</div>
	  <div class="titleblock">
	    <div class=pagetitle><?= $familyInfo["CommonName"] ?></div>
        <div class=metadata><?= $familyInfo["LatinName"] ?></div>
        <div class=metadata>
          locations:
            <a href="./familydetail.php?view=locations&state=<?= $state ?>&family=<?= $familyid ?>">list</a> |
            <a href="./familydetail.php?view=locationsbymonth&state=<?= $state ?>&family=<?= $familyid ?>">by month</a> |
	        <a href="./familydetail.php?view=locationsbyyear&state=<?= $state ?>&family=<?= $familyid ?>">by year</a> |
	        <a href="./familydetail.php?view=map&state=<?= $state ?>&family=<?= $familyid ?>">map</a> <br/>
          species:	
            <a href="./familydetail.php?view=species&state=<?= $state ?>&family=<?= $familyid ?>">list</a> |
	        <a href="./familydetail.php?view=speciesbymonth&state=<?= $state ?>&family=<?= $familyid ?>">by month</a> |
	        <a href="./familydetail.php?view=speciesbyyear&state=<?= $state ?>&family=<?= $familyid ?>">by year</a><br/>
	    </div>
      </div>


<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading($speciesQuery->getSpeciesCount(), "species");
    formatSpeciesListWithPhoto($speciesQuery);

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
	$locationQuery->formatTwoColumnLocationList(true);

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
	$locationQuery->setFromRequest($_GET);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByMonthTable();
}
else if ($view == "map")
{
	$map = new Map("./familydetail.php");
	$map->setFromRequest($_GET);
	$map->draw();
}

footer();
?>

    </div>

<? htmlFoot(); ?>
