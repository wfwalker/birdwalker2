<?

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./sightingquery.php");

$request = new Request;

$sightingQuery = new SightingQuery($request);

$dbQuery = $sightingQuery->performQuery();

htmlHead($sightingQuery->getPageTitle());

globalMenu();
navTrailBirds();
?>

    <div class=contentright>
      <div class=pagesubtitle><?= $pageSubtitle ?></div>
      <div class="titleblock">	  
          <div class=pagetitle><?= $sightingQuery->getPageTitle() ?></div>
      </div>

      <div class=heading><?= mysql_num_rows($dbQuery) ?> Sightings</div>

<table class=report-content width="600px">
<?php
while($sightingInfo = mysql_fetch_array($dbQuery)) {
?>
    <tr>
    <td nowrap> 
<?
	if ($prevSightingInfo["TripDate"] != $sightingInfo["TripDate"]) {
?>
        <a href="./tripdetail.php?tripid=<?= $sightingInfo["tripid"] ?>"><?= $sightingInfo["niceDate"] ?></a>
<?
	}
?>
    </td>
    <td>
<?

 if ($speciesid == "") { echo $sightingInfo["CommonName"]; }

    editLink("./sightingedit.php?id=" . $sightingInfo["sightingid"]);

	if ($sightingInfo["Photo"] == "1") {
?>
        <?= getPhotoLinkForSightingInfo($sightingInfo) ?>
<?
    }
?>
    </td>
    <td nowrap>
<?
    if (($locationid == "") && ($prevSightingInfo["LocationName"] != $sightingInfo["LocationName"])) {
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
    <tr><td></td><td class=report-content><?= $sightingInfo["Notes"] ?></td></tr>
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
