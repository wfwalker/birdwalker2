
<?php

require("./birdwalker.php");
$speciesID = $_GET['id'];

$speciesInfo = getSpeciesInfo($speciesID);
$sightingQuery = performQuery("select * from sighting where sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "' and Photo='1' order by TripDate desc");
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $speciesInfo["CommonName"] ?> photos</title>
</head>

<body>

<?php globalMenu(); disabledBrowseButtons(); navTrailPhotos(); ?>

<div class="contentright">
  <div class="titleblock">
    <div class=pagetitle>
      <a href="./speciesdetail.php?id=<?= $speciesInfo["objectid"] ?>"><?= $speciesInfo["CommonName"] ?></a>
    </div>
  </div>


<?php

while ($sightingInfo = mysql_fetch_array($sightingQuery))
{
	$tripInfo = performOneRowQuery("select *, date_format(Date, '%W,  %M %e, %Y') as niceDate from trip where Date='" . $sightingInfo["TripDate"] . "'");
	$tripYear =  substr($tripInfo["Date"], 0, 4);
	$locationInfo = performOneRowQuery("select * from location where Name='" . $sightingInfo["LocationName"] . "'");
?>
    <div class=heading>
    <div class=pagesubtitle><a href="./tripdetail.php?id=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["niceDate"] ?></a></div>
    <div class=metadata>
    <a href="./locationdetail.php?id=<?= $locationInfo["objectid"] ?>"><?= $locationInfo["Name"] ?></a>
<?
	if (getEnableEdit()) {
?>
        <div><a href="./sightingedit.php?id=<?= $sightingInfo["objectid"] ?>">edit</a></div>
<?
    }
?>
	</div>
    </div>
<?
	if ($sightingInfo["Photo"] == "1") {
		$photoFilename = getPhotoFilename($sightingInfo);

		list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename);
?>
		<img width=<?= $width ?> height=<?= $height ?> src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>">
<?
	}
}
?>

</div>
</body>
</html>
