<?php
require_once("./birdwalker.php");
require_once("./request.php");
require_once("./map.php");
require_once("./sightingquery.php");
require_once("./speciesquery.php");

$request = new Request;

$tripInfo = $request->getTripInfo();
$tripYear = substr($tripInfo["Date"], 0, 4);

htmlHead( $tripInfo["Name"]);

$request->globalMenu();
?>


    <div id="topright-trip">
      <? tripBrowseButtons("./tripdetail.php", $request->getTripInfo(), $request->getView()); ?>
        <div class="pagetitle">
            <?= $tripInfo["Name"] ?>
            <?= editLink("./tripedit.php?tripid=" . $request->getTripID()); ?>
        </div>
        <div class="pagesubtitle"><?= $tripInfo["niceDate"] ?></div>
      <?= $request->viewLinks("speciesbylocation"); ?>
	</div>

    <div id="contentright">
	    <div class="heading">Notes</div>
        <div class="leftcolumn">
		  <div class="report-content">Led by <?= $tripInfo["Leader"] ?></div>
		  <div class="report-content"><? referenceURL($tripInfo); ?></div>
<?          if ($tripInfo["Notes"] != "")
		    { ?>
              <div class="report-content"><?= $tripInfo["Notes"] ?></div>
<?          } ?>
        </div>

<?

require_once("flickr.php");
insertFlickrTripLink($tripInfo);

if ($request->getView() == "speciesbylocation")
{
  $sightingQuery = new SightingQuery($request);

  $locationQuery = new LocationQuery($request);
  $locationCount = $locationQuery->getLocationCount();
  
  $firstSightings = getFirstSightings();
  $firstYearSightings = getFirstYearSightings($tripYear);
  
  // how many  birds were on this trip?
  $tripSightings = $sightingQuery->performQuery();
  
  // how many life birds were on this trip?
  $tripFirstSightings = 0;
  while($sightingInfo = mysql_fetch_array($tripSightings)) {
	if (array_key_exists($sightingInfo['objectid'], $firstSightings)) { $tripFirstSightings++; }
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
        <a href="./locationdetail.php?locationid=<?= $locationInfo["objectid"]?>"><?= $locationInfo["Name"] ?></a>
    </div>

<?
	   $speciesQuery->formatTwoColumnSpeciesList($firstSightings, $firstYearSightings);
	}
}
else
{
    $request->handleStandardViews();
}

footer();

?>

    </div>

<?
htmlFoot();
?>

