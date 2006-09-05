
<?php

require_once("./request.php");
require_once("./birdwalker.php");

$request = new Request;

$siteInfo = $request->getLocationInfo();

$locationSightings = performQuery("Get Location Sightings",
    "SELECT sighting.objectid FROM sighting, location
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

<div id="topright-location">
  <? locationBrowseButtons("./locationdetail.php", $request->getLocationID(), $request->getView()); ?>

    <div class="pagetitle">
        <?= $siteInfo["Name"] ?>
	    <? editLink("./locationcreate.php?locationid=" . $request->getLocationID()); ?>
    </div>
	<div class="pagesubtitle">
	    <?= $siteInfo["County"] ?> County, <?= $siteInfo["State"] ?>
	</div>
  <? $request->viewLinks("species"); ?>
</div>

<div id="contentright">
<? if ($siteInfo["noteworthy"] != 0) { ?>
      <div class="heading">Notes</div>
	  <div class="leftcolumn">
        <div class="report-content"><? referenceURL($siteInfo); ?></div>
<?      if ($siteInfo["Notes"] != "") { ?>
          <div class="report-content"><?= $siteInfo["Notes"] ?></div>
<?      } ?>
      </div> <?
   }

$request->handleStandardViews();
footer();

?>

</div>

<?
htmlFoot();
?>
