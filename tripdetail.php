
<?php

require_once("./birdwalker.php");
require_once("./map.php");
require_once("./sightingquery.php");
require_once("./speciesquery.php");

$tripID = param($_GET, 'tripid', 343);
$view = param($_GET, 'view', 'list');

$tripInfo = getTripInfo($tripID);
$tripYear = substr($tripInfo["Date"], 0, 4);
$tripMonth = substr($tripInfo["Date"], 5, 2);

$sightingQuery = new SightingQuery;
$sightingQuery->setFromRequest($_GET);

$locationQuery = new LocationQuery;
$locationQuery->setFromRequest($_GET);
$locationCount = $locationQuery->getLocationCount();

$firstSightings = getFirstSightings();
$firstYearSightings = getFirstYearSightings($tripYear);

// how many  birds were on this trip?
$tripSightings = $sightingQuery->performQuery();

// total species count for this trip
$tripSpeciesCount = mysql_num_rows($tripSightings);

// how many life birds were on this trip?
$tripFirstSightings = 0;
while($sightingInfo = mysql_fetch_array($tripSightings)) {
	if ($firstSightings[$sightingInfo['objectid']] != null) { $tripFirstSightings++; }
}

htmlHead( $tripInfo["Name"]);

globalMenu();
tripBrowseButtons("./tripdetail.php", $tripID, $view);
$items[] = "<a href=\"./tripindex.php#" . $tripYear . "\">" . $tripYear . "</a>";
$items[] = "<a href=\"./monthdetail.php?view=trip&year=" . $tripYear . "&month=" . $tripMonth . "\">" . strtolower(getMonthNameForNumber($tripMonth)) . "</a>";
navTrailTrips($items);
?>


    <div class="contentright">
      <div class=pagesubtitle>Trip Detail</div>

	  <div class=titleblock>

<?      if (($view != "map") && ($view != "photo")) { $sightingQuery->rightThumbnail(true); }?>
        <div class=pagetitle>
            <?= $tripInfo["Name"] ?>
            <?= editLink("./tripedit.php?tripid=" . $tripID); ?>
        </div>
        <div class=metadata>
          <?= $tripInfo["niceDate"] ?><br/>
          Led by  <?= $tripInfo["Leader"] ?>
<?        referenceURL($tripInfo); ?>
        </div>
        <div class=metadata>
	        <a href="./tripdetail.php?view=list&tripid=<?= $tripID ?>">list</a> | 
	        <a href="./tripdetail.php?view=photo&tripid=<?= $tripID ?>">photo</a> |
            <a href="./tripdetail.php?view=map&tripid=<?= $tripID ?>">map</a><br/>
        </div>


         <div class=report-content><p><?= $tripInfo["Notes"] ?></p></div>
      </div>


<?


if ($view == "photo")
{
	$sightingQuery->formatPhotos();
}
else if ($view == "map")
{
	$map = new Map("./tripdetail.php");
	$map->setFromRequest($_GET);
	$map->draw();
}
else if ($view="list")
{
	if ($locationCount > 1) {
		doubleCountHeading($tripSpeciesCount, "species", $tripFirstSightings, "life bird");
	}

	$dbLocation = $locationQuery->performQuery();
	while($locationInfo = mysql_fetch_array($dbLocation))
	{
		$speciesQuery = new SpeciesQuery;
		$speciesQuery->setTripID($tripID);
		$speciesQuery->setLocationID($locationInfo["objectid"]);

		$locationSightingQuery = new SightingQuery;
		$locationSightingQuery->setTripID($tripID);
		$locationSightingQuery->setLocationID($locationInfo["objectid"]);

		$dbLocationSightings = $locationSightingQuery->performQuery();

		$tripLocationCount = mysql_num_rows($dbLocationSightings);

		$locationFirstSightings = 0;
		while($sightingInfo = mysql_fetch_array($dbLocationSightings)) {
			if ($firstSightings[$sightingInfo['sightingid']] != null) { $locationFirstSightings++; }
		}
 ?>

    <div class="heading">
        <a href="./locationdetail.php?locationid=<?= $locationInfo["objectid"]?>"><?= $locationInfo["Name"] ?></a>,
        <?= $tripLocationCount ?> species<? if ($locationFirstSightings > 0) { ?>,
        <?= $locationFirstSightings ?> life bird<? if ($locationFirstSightings > 1) echo 's'; } ?>
    </div>

<?
        $speciesQuery->formatTwoColumnSpeciesList();
	}
}

footer();

?>

    </div>

<?
htmlFoot();
?>
