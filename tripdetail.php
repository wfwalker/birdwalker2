
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./map.php");
require_once("./sightingquery.php");
require_once("./speciesquery.php");

$request = new Request;

$tripInfo = $request->getTripInfo();

$tripYear = substr($tripInfo["Date"], 0, 4);
$tripMonth = substr($tripInfo["Date"], 5, 2);

$sightingQuery = new SightingQuery($request);

$locationQuery = new LocationQuery($request);
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
	if (array_key_exists($sightingInfo['objectid'], $firstSightings)) { $tripFirstSightings++; }
}

htmlHead( $tripInfo["Name"]);

globalMenu();
$items[] = "<a href=\"./tripindex.php#" . $tripYear . "\">" . $tripYear . "</a>";
$items[] = "<a href=\"./monthdetail.php?view=trip&year=" . $tripYear . "&month=" . $tripMonth . "\">" . strtolower(getMonthNameForNumber($tripMonth)) . "</a>";
navTrailTrips($items);
?>


    <div class="contentright">
      <? tripBrowseButtons("./tripdetail.php", $request->getTripID(), $request->getView()); ?>

	  <div class=titleblock>

<?      if (($request->getView() != "map") && ($request->getView() != "photo")) { $sightingQuery->rightThumbnail(true); }?>
        <div class=pagetitle>
            <?= $tripInfo["Name"] ?>
            <?= editLink("./tripedit.php?tripid=" . $request->getTripID()); ?>
        </div>
        <div class=metadata>
          <?= $tripInfo["niceDate"] ?><br/>
          Led by  <?= $tripInfo["Leader"] ?>
<?        referenceURL($tripInfo); ?>
        </div>
        <div class=metadata>
	        <a href="./tripdetail.php?view=list&tripid=<?= $request->getTripID() ?>">list</a> | 
	        <a href="./tripdetail.php?view=photo&tripid=<?= $request->getTripID() ?>">photo</a> |
            <a href="./tripdetail.php?view=map&tripid=<?= $request->getTripID() ?>">map</a><br/>
        </div>


         <div class=report-content><p><?= $tripInfo["Notes"] ?></p></div>
      </div>

<?

if ($request->getView() == "photo")
{
	$sightingQuery->formatPhotos();
}
else if ($request->getView() == "map")
{
	$map = new Map("./tripdetail.php", $request);
	$map->draw();
}
else if ($request->getView() == "list")
{
	if ($locationCount > 1) {
		doubleCountHeading($tripSpeciesCount, "species", $tripFirstSightings, "life bird");
	}

	$dbLocation = $locationQuery->performQuery();
	while($locationInfo = mysql_fetch_array($dbLocation))
	{
		$speciesQuery = new SpeciesQuery($request);
		$speciesQuery->mReq->setLocationID($locationInfo["objectid"]);

		$locationSightingQuery = new SightingQuery($request);
		$locationSightingQuery->mReq->setLocationID($locationInfo["objectid"]);

		$dbLocationSightings = $locationSightingQuery->performQuery();

		$tripLocationCount = mysql_num_rows($dbLocationSightings);

		$locationFirstSightings = 0;
		while($sightingInfo = mysql_fetch_array($dbLocationSightings)) {
			if (array_key_exists($sightingInfo['sightingid'], $firstSightings)) { $locationFirstSightings++; }
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
