
<?php

require("./birdwalker.php");
$locationID = $_GET['id'];

$siteInfo = getLocationInfo($locationID);
$sightingQuery = performQuery("
    SELECT * from sighting
      WHERE sighting.LocationName='" . $siteInfo["Name"] . "' AND Photo='1'
      ORDER BY TripDate DESC");
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $siteInfo["Name"] ?> photos</title>
</head>

<body>

<?
globalMenu();
locationBrowseButtons($siteInfo, $locationID, "photo");
navTrailLocationDetail($siteInfo);
?>

<div class="contentright">
  <div class="titleblock">
    <div class=pagetitle>
      <a href="./locationdetail.php?id=<?= $siteInfo["objectid"] ?>"><?= $siteInfo["Name"] ?></a>
    </div>

<?    referenceURL($siteInfo);
      mapLink($siteInfo);
      locationViewLinks($locationID); ?>
  </div>

  <div class="heading"><?= mysql_num_rows($sightingQuery) ?> Photo<?= mysql_num_rows($sightingQuery) != 1 ? 's': '' ?></div>

<?
while ($sightingInfo = mysql_fetch_array($sightingQuery))
{
	$tripInfo = performOneRowQuery("
        SELECT *, date_format(Date, '%W,  %M %e, %Y') AS niceDate FROM trip WHERE Date='" . $sightingInfo["TripDate"] . "'");
	$speciesInfo = performOneRowQuery("
        SELECT * FROM species WHERE Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
	$tripYear =  substr($tripInfo["Date"], 0, 4); ?>

    <div class=heading>
    <div class=pagesubtitle>
      <a href="./tripdetail.php?id=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["niceDate"] ?></a>
<?    editLink("./sightingedit.php?id=" . $sightingInfo["objectid"]); ?>
    </div>
    <div class=metadata>
    <a href="./speciesdetail.php?id=<?= $speciesInfo["objectid"] ?>"><?= $speciesInfo["CommonName"] ?></a>


	</div>
    </div>

<?	$photoFilename = getPhotoFilename($sightingInfo);

	list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

	<img width=<?= $width ?> height=<?= $height ?> src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>">
<?
} ?>

</div>
</body>
</html>
