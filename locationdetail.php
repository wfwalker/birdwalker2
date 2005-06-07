
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

globalMenu();
$request->navTrailLocations();
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

      trips:
	  <?= $request->linkToSelfChangeView("trips", "list"); ?> | 
	  <?= $request->linkToSelfChangeView("tripsummaries", "summaries"); ?><br/>

      species:
	  <?= $request->linkToSelfChangeView("species", "list"); ?> | 
	  <?= $request->linkToSelfChangeView("chrono", "ABA"); ?> | 
	  <?= $request->linkToSelfChangeView("speciesbymonth", "by month"); ?> | 
	  <?= $request->linkToSelfChangeView("speciesbyyear", "by year"); ?> | 
	  <?= $request->linkToSelfChangeView("photo", "photo"); ?><br/>
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
