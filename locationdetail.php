<?php

require_once("./request.php");
require_once("./birdwalker.php");

$request = new Request;

$siteInfo = $request->getLocationInfo();

$locationSightings = performQuery("Get Location Sightings",
    "SELECT sightings.id FROM sightings, locations
      WHERE sightings.location_id=locations.id
      AND locations.id='" . $request->getLocationID() ."'");

$firstSightings = getFirstSightings();
$locationFirstSightings = 0;

while($sightingInfo = mysql_fetch_array($locationSightings)) {
	if (array_key_exists($sightingInfo['id'], $firstSightings)) {
		$locationFirstSightings++;
	}
}

htmlHead($siteInfo["name"]);

$request->globalMenu();
?>

<div id="topright-location">
  <? locationBrowseButtons("./locationdetail.php", $request->getLocationID(), $request->getView()); ?>

    <div class="pagetitle">
        <?= $siteInfo["name"] ?>
	    <? editLink("./locationcreate.php?locationid=" . $request->getLocationID()); ?>
    </div>
	<div class="pagesubtitle">
	    <?= $siteInfo["county"] ?> County, <?= $siteInfo["state"] ?>
	</div>
  <? $request->viewLinks("species"); ?>
</div>

<div id="contentright">
<? if ($siteInfo["noteworthy"] != 0) { ?>
      <div class="heading">Notes</div>
	  <div class="leftcolumn">
        <div class="report-content"><? reference_url($siteInfo); ?></div>
<?	    if (getEnableEdit()) { ?>
          <div class="report-content"><a href="./tripcreate.php?locationid=<?=$request->getLocationID()?>">create trip</a></div>
<?      }
        if ($siteInfo["notes"] != "") { ?>
		    <div class="report-content"><?= stripslashes($siteInfo["notes"]) ?></div>
<?      }
 ?>
      </div>

<?   }

$request->handleStandardViews();
footer();

?>

</div>

<?
htmlFoot();
?>
