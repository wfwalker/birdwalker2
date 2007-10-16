<?php

require_once("./request.php");
require_once("./birdwalker.php");

$request = new Request;

$siteInfo = $request->getLocationInfo();

$locationSightings = performQuery("Get Location Sightings",
    "SELECT sighting.id FROM sighting, location
      WHERE sighting.location_id=location.id
      AND location.id='" . $request->getLocationID() ."'");

$firstSightings = getFirstSightings();
$locationFirstSightings = 0;

while($sightingInfo = mysql_fetch_array($locationSightings)) {
	if (array_key_exists($sightingInfo['id'], $firstSightings)) {
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
<?	    if (getEnableEdit()) { ?>
          <div class="report-content"><a href="./tripcreate.php?locationid=<?=$request->getLocationID()?>">create trip</a></div>
<?      }
        if ($siteInfo["Notes"] != "") { ?>
		    <div class="report-content"><?= stripslashes($siteInfo["Notes"]) ?></div>
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
