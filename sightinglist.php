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
?>
      <tr>
      <td nowrap> 
<?
	if ($prevSightingInfo == "" || $prevSightingInfo["TripDate"] != $sightingInfo["TripDate"]) {
?>
        <a href="./tripdetail.php?tripid=<?= $sightingInfo["tripid"] ?>"><?= $sightingInfo["niceDate"] ?></a>
<?
	}
?>
    </td>
    <td>
<?

    if ($request->getSpeciesID() == "") { echo $sightingInfo["CommonName"]; }

    editLink("./sightingedit.php?id=" . $sightingInfo["sightingid"]);

	if ($sightingInfo["Photo"] == "1") {
?>
        <?= getPhotoLinkForSightingInfo($sightingInfo, "sightingid") ?>
<?
    }
?>
    </td>
    <td nowrap>
<?
	  if (($request->getLocationID() == "") && ($prevSightingInfo == "" || $prevSightingInfo["LocationName"] != $sightingInfo["LocationName"])) {
?>
		<?= $sightingInfo["LocationName"] ?>, <?= $sightingInfo["County"] ?> County, <?= $sightingInfo["State"] ?>
<?
	}
?>
	</td>
    </tr>
<?	
	if ($sightingInfo["Notes"] != "") {
?>
        <tr><td></td><td class="report-content"><?= stripslashes($sightingInfo["Notes"]) ?></td></tr>
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
