
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./sightingquery.php");
require_once("./tripquery.php");
require_once("./map.php");

$locationID = param($_GET, 'locationid', 1);
$view = param($_GET, 'view', 'list');

$siteInfo = getLocationInfo($locationID);

$locationSightings = performQuery("
    SELECT sighting.objectid FROM sighting, location
      WHERE sighting.LocationName=location.Name
      AND location.objectid='" . $locationID ."'");

$firstSightings = getFirstSightings();
$locationFirstSightings = 0;

while($sightingInfo = mysql_fetch_array($locationSightings)) {
	if ($firstSightings[$sightingInfo['objectid']] != null) {
		$locationFirstSightings++;
	}
}

htmlHead($siteInfo["Name"]);

globalMenu();
locationBrowseButtons("./locationdetail.php", $locationID, $view);
navTrailLocationDetail($siteInfo);
?>

<div class="contentright">
  <div class="pagesubtitle">Location Detail</div>
  <div class="titleblock">
<?  if (($view != "map") && ($view != "photo")) { rightThumbnailLocation($siteInfo["Name"]); } ?>
    <div class=pagetitle>
        <?= $siteInfo["Name"] ?>
        <? editLink("./locationcreate.php?locationid=" . $locationID); ?>
    </div>

<? referenceURL($siteInfo);
   if ($siteInfo["Latitude"] > 0)
   {
	   $lat = $siteInfo["Latitude"];
	   $long = $siteInfo["Longitude"];
?>
   <div>maps: 
      <a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude=<?= $lat ?>&longitude=<?= $long ?>">mapquest</a> |
      <a href="http://terraserver.microsoft.com/image.aspx?Lon=<?=$long?>&Lat=<?=$lat?>&w=1">terraserver</a> |
		<a href="./locationdetail.php?view=map&locationid=<?=$locationID?>">opengis</a>
    </div>
<? } ?>

      species: <a href="./locationdetail.php?locationid=<?=$locationID?>">list</a> |
      <a href="./locationdetail.php?view=bymonth&locationid=<?=$locationID?>">by month</a> |
      <a href="./locationdetail.php?view=byyear&locationid=<?=$locationID?>">by year</a> |
      <a href="./locationdetail.php?view=photo&locationid=<?=$locationID?>">photos</a>
    </div>

    <div class=report-content><?= $siteInfo["Notes"] ?></div>

<?
	if ($view == "list")
	{
		$tripQuery = new TripQuery;
		$tripQuery->setFromRequest($_GET);
		$tripCount = $tripQuery->getTripCount();

		$speciesQuery = new SpeciesQuery;
		$speciesQuery->setFromRequest($_GET);

		doubleCountHeading($speciesQuery->getSpeciesCount(), "species", $locationFirstSightings, "life bird");
		$speciesQuery->formatTwoColumnSpeciesList();
		countHeading($tripCount, "trip");
		$tripQuery->formatTwoColumnTripList();
	}
	else if ($view == "bymonth")
	{
		$speciesQuery = new SpeciesQuery;
		$speciesQuery->setFromRequest($_GET);
		doubleCountHeading($speciesQuery->getSpeciesCount(), "species", $locationFirstSightings, "life bird");
		$speciesQuery->formatSpeciesByMonthTable();
	}
	else if ($view == "byyear")
	{
		$speciesQuery = new SpeciesQuery;
		$speciesQuery->setFromRequest($_GET);
		doubleCountHeading($speciesQuery->getSpeciesCount(), "species", $locationFirstSightings, "life bird");
		$speciesQuery->formatSpeciesByYearTable();
	}
	else if ($view == "photo")
	{
		$sightingQuery = new SightingQuery;
		$sightingQuery->setFromRequest($_GET);
		$sightingQuery->formatPhotos();
	}
    else if ($view == "map")
	{
		$map = new Map("./locationdetail.php");
		$map->setFromRequest($_GET);
		$map->draw();
	}

footer();

?>

</div>

<?
htmlFoot();
?>
