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
        <div class="onecolumn">
		  <div class="report-content">Led by <?= $tripInfo["Leader"] ?></div>
		  <div class="report-content"><? referenceURL($tripInfo); ?></div>
<?          if ($tripInfo["Notes"] != "")
		    { ?>
              <div class="report-content"><?= stripslashes($tripInfo["Notes"]) ?></div>
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

	$dbLocation = $locationQuery->performQuery();
	while($locationInfo = mysql_fetch_array($dbLocation))
	{
		$speciesQuery = new SpeciesQuery($request);
		$speciesQuery->mReq->setLocationID($locationInfo["id"]);

		$locationSightingQuery = new SightingQuery($request);
		$locationSightingQuery->mReq->setLocationID($locationInfo["id"]);

		$dbLocationSightings = $locationSightingQuery->performQuery();

		$tripLocationCount = mysql_num_rows($dbLocationSightings);
 ?>

    <div class="heading">
        <a href="./locationdetail.php?locationid=<?= $locationInfo["id"]?>"><?= $locationInfo["Name"] ?></a>
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

