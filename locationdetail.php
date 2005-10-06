
<?php

require_once("./request.php");
require_once("./birdwalker.php");

$request = new Request;

$siteInfo = $request->getLocationInfo();

$locationSightings = performQuery("
    SELECT sighting.objectid FROM sighting, location
      WHERE sighting.LocationName=location.Name
      AND location.objectid='" . $request->getLocationID() ."'");

$firstSightings = getFirstSightings();
$locationFirstSightings = 0;

while($sightingInfo = mysql_fetch_array($locationSightings)) {
	if (array_key_exists($sightingInfo['objectid'], $firstSightings)) {
		$locationFirstSightings++;
	}
}

htmlHead($siteInfo["Name"]);

$request->globalMenu();
?>

<div class="topright">
  <? locationBrowseButtons("./locationdetail.php", $request->getLocationID(), $request->getView()); ?>

    <div class="pagetitle">
        <?= $siteInfo["Name"] ?>
	    <? editLink("./locationcreate.php?locationid=" . $request->getLocationID()); ?>
    </div>
	<div class="pagesubtitle">
	    <?= $siteInfo["County"] ?> County, <?= $siteInfo["State"] ?>
	</div>
</div>

<div class="contentright">
<?  if (($request->getView() != "map") && ($request->getView() != "photo")) { rightThumbnailLocation($siteInfo["Name"]); } ?>

  <? $request->viewLinks("species"); ?>

  <div class="titleblock">

<? referenceURL($siteInfo);
   if ($siteInfo["Latitude"] > 0)
   {
	   $lat = $siteInfo["Latitude"];
	   $long = $siteInfo["Longitude"];
?>
   <div class="viewlinks">maps: 
      <a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude=<?= $lat ?>&longitude=<?= $long ?>">mapquest</a> |
      <a href="http://terraserver.microsoft.com/image.aspx?Lon=<?=$long?>&Lat=<?=$lat?>&w=1">terraserver</a> |
		<a href="./locationdetail.php?view=map&locationid=<?=$request->getLocationID()?>">opengis</a>
    </div>
<? } ?>

    </div>

    <div class=report-content><?= $siteInfo["Notes"] ?></div>

<?
$request->handleStandardViews();
footer();

?>

</div>

<?
htmlFoot();
?>
