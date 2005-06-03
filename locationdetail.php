
<?php

require_once("./request.php");
require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./sightingquery.php");
require_once("./chronolist.php");
require_once("./tripquery.php");
require_once("./map.php");

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

globalMenu();
navTrailLocationDetail($siteInfo, $request->getView());
?>

<div class="contentright">
<? locationBrowseButtons("./locationdetail.php", $request->getLocationID(), $request->getView()); ?>
  <div class="titleblock">
<?  if (($request->getView() != "map") && ($request->getView() != "photo")) { rightThumbnailLocation($siteInfo["Name"]); } ?>
    <div class=pagetitle>
        <?= $siteInfo["Name"] ?>
	    <? editLink("./locationcreate.php?locationid=" . $request->getLocationID()); ?>
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
		<a href="./locationdetail.php?view=map&locationid=<?=$request->getLocationID()?>">opengis</a>
    </div>
<? } ?>

      species:
      <a href="./locationdetail.php?view=species&locationid=<?=$request->getLocationID()?>">list</a> |
      <a href="./locationdetail.php?view=chrono&locationid=<?=$request->getLocationID()?>">ABA</a> |
      <a href="./locationdetail.php?view=speciesbymonth&locationid=<?=$request->getLocationID()?>">by month</a> |
      <a href="./locationdetail.php?view=speciesbyyear&locationid=<?=$request->getLocationID()?>">by year</a> |
      <a href="./locationdetail.php?view=photo&locationid=<?=$request->getLocationID()?>">photos</a>
    </div>

    <div class=report-content><?= $siteInfo["Notes"] ?></div>

<?
$request->handleStandardViews("species");
footer();

?>

</div>

<?
htmlFoot();
?>
