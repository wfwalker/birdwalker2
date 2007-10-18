<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./sightingquery.php");

$request = new Request;

$sightingQuery = new SightingQuery($request);

$dbQuery = $sightingQuery->performQuery();

htmlHead($request->getPageTitle());
$request->globalMenu();

?>

    <div id="topright-trip">
        <div class="pagekind">Sighting List</div>
        <div class="pagetitle"><?= $sightingQuery->getPageTitle() ?></div>
    </div>

    <div id="contentright">
      <div class="heading"><?= mysql_num_rows($dbQuery) ?> Sightings</div>

      <table class="report-content" width="600px">
<?
     $prevSightingInfo = "";
     while($sightingInfo = mysql_fetch_array($dbQuery)) {
	   $locationInfo = getLocationInfo($sightingInfo["location_id"]);
	   $speciesInfo = getSpeciesInfo($sightingInfo["species_id"]);
	   $tripInfo = getTripInfo($sightingInfo["trip_id"]);
?>
      <tr>
      <td nowrap> 
<?
	if ($prevSightingInfo == "" || $prevSightingInfo["trip_id"] != $sightingInfo["trip_id"]) {
?>
        <a href="./tripdetail.php?tripid=<?= $sightingInfo["trip_id"] ?>"><?= $tripInfo["date"] ?></a>
<?
	}
?>
    </td>
    <td>
<?

    if ($request->getSpeciesID() == "") { echo $speciesInfo["common_name"]; }

    editLink("./sightingedit.php?id=" . $sightingInfo["id"]);

	if ($sightingInfo["Photo"] == "1") {
?>
        <?= getPhotoLinkForSightingInfo($sightingInfo, "sightingid") ?>
<?
    }
?>
    </td>
    <td nowrap>
<?
	  if (($request->getLocationID() == "") && ($prevSightingInfo == "" || $prevSightingInfo["location_id"] != $sightingInfo["location_id"])) {
?>
		<?= $locationInfo["name"] ?>, <?= $locationInfo["county"] ?> County, <?= $locationInfo["state"] ?>
<?
	}
?>
	</td>
    </tr>
<?	
	if ($sightingInfo["notes"] != "") {
?>
        <tr><td></td><td class="report-content"><?= stripslashes($sightingInfo["notes"]) ?></td></tr>
<?
    }

	$prevSightingInfo = $sightingInfo;
}

?>

  <table>

<?
footer();
?>

    </div>

<?
htmlFoot();
?>
