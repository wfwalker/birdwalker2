<html>

<?php

require("./birdwalker.php");
require("./locationquery.php");
require("./sightingquery.php");
require("./speciesquery.php");

$tripID = param($_GET, 'id', 343);
$view = param($_GET, 'view', 'list');

$tripInfo = getTripInfo($tripID);
$tripYear = substr($tripInfo["Date"], 0, 4);

$sightingQuery = new SightingQuery;
$sightingQuery->setTripID($tripID);

$locationQuery = new LocationQuery;
$locationQuery->setTripID($tripID);
$locationCount = $locationQuery->getLocationCount();
$extrema = $locationQuery->findExtrema();

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

?>

  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $tripInfo["Name"] ?></title>
  </head>

  <body>

<?php
globalMenu();
tripBrowseButtons($tripID, $view);
$items[] = "<a href=\"./tripindex.php#" . $tripYear . "\">" . $tripYear . "</a>";
navTrailTrips($items);
?>


    <div class="contentright">
      <div class=pagesubtitle> <?= $tripInfo["niceDate"] ?></div>

	  <div class=titleblock>

<?      if ($view != "photo") { $sightingQuery->rightThumbnail(true); }?>
        <div class=pagetitle>
            <?= $tripInfo["Name"] ?>
            <?= editLink("./tripedit.php?id=" . $tripID); ?>
        </div>
        <div class=metadata>
          Led by  <?= $tripInfo["Leader"] ?>
<?        referenceURL($tripInfo); ?>
        </div>
        <div class=metadata>
	        <a href="./tripdetail.php?view=list&id=<?= $tripID ?>"?>list</a> | 
	        <a href="./tripdetail.php?view=photo&id=<?= $tripID ?>"?>photo</a> |
            <a href="./locationmap.php?minlat=<?= $extrema["minLat"]-0.01 ?>&maxlat=<?= $extrema["maxLat"]+0.01 ?>&minlong=<?= $extrema["minLong"]-0.01 ?>&maxlong=<?= $extrema["maxLong"]+0.01 ?>">map</a><br/>
        </div>


         <div class=report-content><p><?= $tripInfo["Notes"] ?></p></div>
      </div>


<?

if ($locationCount > 1) {
	doubleCountHeading($tripSpeciesCount, "species", $tripFirstSightings, "life bird");
}

if ($view == "photo")
{
	$sightingQuery->formatPhotos();
}
else
{
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
        <a href="./locationdetail.php?id=<?= $locationInfo["objectid"]?>"><?= $locationInfo["Name"] ?></a>,
        <?= $tripLocationCount ?> species<? if ($locationFirstSightings > 0) { ?>,
        <?= $locationFirstSightings ?> life bird<? if ($locationFirstSightings > 1) echo 's'; } ?>
    </div>

<?
        $speciesQuery->formatTwoColumnSpeciesList();
	}
}
?>

    </div>
  </body>
</html>
