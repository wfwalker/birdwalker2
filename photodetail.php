
<?php

require("./birdwalker.php");
$sightingID = $_GET['id'];

$sightingInfo = getSightingInfo($sightingID);
$speciesInfo = performOneRowQuery("select * from species where Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
$tripInfo = performOneRowQuery("select *, date_format(Date, '%W,  %M %e, %Y') as niceDate from trip where Date='" . $sightingInfo["TripDate"] . "'");
$tripYear =  substr($tripInfo["Date"], 0, 4);
$locationInfo = performOneRowQuery("select * from location where Name='" . $sightingInfo["LocationName"] . "'");

$firstPhotoSightingID = performCount("select objectid from sighting where Photo='1' order by concat(TripDate,objectid)");
$lastPhotoSightingID = performCount("select objectid from sighting where Photo='1' order by concat(TripDate, objectid) desc");
$nextPhotoSightingID = performCount("select objectid from sighting where Photo='1' and concat(TripDate,objectid) > '" . $sightingInfo["TripDate"] . $sightingID . "' order by concat(TripDate,objectid)");
$prevPhotoSightingID = performCount("select objectid from sighting where Photo='1' and concat(TripDate,objectid) < '" . $sightingInfo["TripDate"] . $sightingID . "' order by concat(TripDate,objectid) desc");

if ($nextPhotoSightingID == "") { $nextPhotoSightingID = $sightingID; }
if ($prevPhotoSightingID == "") { $prevPhotoSightingID = $sightingID; }

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $speciesInfo["CommonName"] ?>,  <?= $tripInfo["niceDate"] ?></title>
</head>

<body>

<?php globalMenu(); browseButtons("./photodetail.php?id=", $sightingID, $firstPhotoSightingID, $prevPhotoSightingID, $nextPhotoSightingID, $lastPhotoSightingID); navTrailPhotos(); ?>

<div class="contentright">
<div class="titleblock">
	  <div class=pagetitle><a href="./speciesdetail.php?id=<?= $speciesInfo["objectid"] ?>"><?= $speciesInfo["CommonName"] ?></a></div>
      <div class=pagesubtitle><a href="./tripdetail.php?id=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["niceDate"] ?></div>
      <div class=metadata>
        <a href="./locationdetail.php?id=<?= $locationInfo["objectid"] ?>"><?= $locationInfo["Name"] ?></a> 
      </div>
<?php if (getEnableEdit()) { echo "<div><a href=\"./sightingedit.php?id=" . $sightingID . "\">edit</a></div>"; } ?>
</div>

<?php

if ($sightingInfo["Photo"] == "1") {
	$photoFilename = getPhotoFilename($sightingInfo);

	list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename);
	echo "<img width=" . $width . " height=" . $height . "  src=\"" . getPhotoURLForSightingInfo($sightingInfo) . "\">";
	echo "<div class=copyright>@2004 W. F. Walker</div>";
}

if (strlen($sightingInfo["Notes"]) > 0) {
  echo "<p class=sighting-notes>" . $sightingInfo["Notes"] . "</p>";
}

if (strlen($tripInfo["Notes"]) > 0) {
	echo "<div class=heading>" . $tripInfo["Name"] . "</div>";
	echo "<p class=sighting-notes>" . $tripInfo["Notes"] . "</p>";
}

if (strlen($locationInfo["Notes"]) > 0) {
	echo "<div class=heading>" . $locationInfo["Name"] . "</div>";
	echo "<p class=sighting-notes>" . $locationInfo["Notes"] . "</p>";
}

if (strlen($speciesInfo["Notes"]) > 0) {
	echo "<div class=heading>" . $speciesInfo["CommonName"] . "</div>";
	echo "<p class=sighting-notes>" . $speciesInfo["Notes"] . "</p>";
}

?>

</div>
</body>
</html>
