<?

require("./birdwalker.php");
require("./sightingquery.php");

$tripid = param($_GET, "tripid", "");
$speciesid = param($_GET, "speciesid", "");
$locationid = param($_GET, "locationid", "");
$year = param($_GET, "year", "");
$month = param($_GET, "month", "");
$county = param($_GET, "county", "");
$state = param($_GET, "state", "");

$sightingQuery = new SightingQuery;
$sightingQuery->setTripID($tripid);
$sightingQuery->setSpeciesID($speciesid);
$sightingQuery->setLocationID($locationid);
$sightingQuery->setYear($year);
$sightingQuery->setMonth($month);
$sightingQuery->setCounty($county);
$sightingQuery->setState($state);

$dbQuery = $sightingQuery->performQuery();
?>

<html>

  <? htmlHead($sightingQuery->getPageTitle()); ?>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

    <div class=contentright>
      <div class=pagesubtitle><?= $pageSubtitle ?></div>
      <div class="titleblock">	  
          <div class=pagetitle><?= $sightingQuery->getPageTitle() ?></div>
      </div>

      <div class=heading><?= mysql_num_rows($dbQuery) ?> Sightings</div>

<table class=report-content columns=4 width="600px">
<?php
while($sightingInfo = mysql_fetch_array($dbQuery)) {
?>
    <tr>
    <td nowrap> 
<?
	if ($prevSightingInfo["TripDate"] != $sightingInfo["TripDate"]) {
?>
        <a href="./tripdetail.php?id=<?= $sightingInfo["tripid"] ?>"><?= $sightingInfo["niceDate"] ?></a>
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
    <tr><td></td><td class=sighting-notes><?= $sightingInfo["Notes"] ?></td></tr>
<?
    }

	$prevSightingInfo = $sightingInfo;
}
?>

    </div>
  </body>
</html>