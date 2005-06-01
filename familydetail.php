
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./tripquery.php");
require_once("./locationquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$familyid = reqParam($_GET, "familyid");
$orderid = floor($familyid / 100);
$view = param($_GET, "view", "species");

$request = new Request;

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
$items[] = "<a href=\"./orderdetail.php?view=" . $view . "&orderid=" . round($orderInfo["objectid"] / pow(10, 9)) . "\">" . strtolower($orderInfo["LatinName"]) . "</a>";
navTrailBirds($items);
 ?>

    <div class=contentright>
	<? browseButtons("Family Detail", "./familydetail.php?view=".$view."&familyid=", $familyid, $firstFamily, $prevFamily, $nextFamily, $lastFamily); ?>

	  <div class="titleblock">
	    <div class=pagetitle><?= $familyInfo["CommonName"] ?></div>
        <div class=metadata><?= $familyInfo["LatinName"] ?></div>
        <div class=metadata>
          locations:
            <a href="./familydetail.php?view=locations&familyid=<?= $familyid ?>">list</a> |
            <a href="./familydetail.php?view=locationsbymonth&familyid=<?= $familyid ?>">by month</a> |
	        <a href="./familydetail.php?view=locationsbyyear&familyid=<?= $familyid ?>">by year</a> |
	        <a href="./familydetail.php?view=map&familyid=<?= $familyid ?>">map</a> <br/>
          species:	
            <a href="./familydetail.php?view=species&familyid=<?= $familyid ?>">list</a> |
	        <a href="./familydetail.php?view=chrono&familyid=<?= $familyid ?>">ABA</a> |
	        <a href="./familydetail.php?view=speciesbymonth&familyid=<?= $familyid ?>">by month</a> |
	        <a href="./familydetail.php?view=speciesbyyear&familyid=<?= $familyid ?>">by year</a><br/>
	    </div>
      </div>


<?
if ($view == 'species' || $view == 'lists' || $view == "photo") // TODO is this a good idea? 
{
	$speciesQuery = new SpeciesQuery($request);
	countHeading($speciesQuery->getSpeciesCount(), "species");
    formatSpeciesListWithPhoto($speciesQuery);

	$tripQuery = new TripQuery($request);
	countHeading( $tripQuery->getTripCount(), "trip");
	$tripQuery->formatTwoColumnTripList();
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery($request);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable(); 
}
elseif ($view == 'speciesbymonth')
{
	$speciesQuery = new SpeciesQuery($request);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery($request);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatTwoColumnLocationList(true);

	$tripQuery = new TripQuery($request);
	countHeading( $tripQuery->getTripCount(), "trip");
	$tripQuery->formatTwoColumnTripList();
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery($request);
	countHeading( $locationQuery->getLocationCount(), "location");
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
	$map = new Map("./familydetail.php", $request);
	$map->draw();
}
else if ($view == "chrono")
{
	$chrono = new ChronoList($request);
	$chrono->draw();
}
else
{
	die("Fatal error: unknown view mode " . $view);
}

footer();
?>

    </div>

<? htmlFoot(); ?>
