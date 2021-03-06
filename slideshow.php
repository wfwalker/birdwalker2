<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./sightingquery.php");

$originPageScript = $_GET["origin"];

$request = new Request;
$sightingQuery = new SightingQuery($request);
$pageTitle =  $sightingQuery->getPageTitle();

if ($request->getSightingID() == "")
{ 
	$request->setSightingID(performCount("Get First Photo Sighting ID",
	$sightingQuery->getSelectClause() . " " .
	$sightingQuery->getFromClause() . " " .
	$sightingQuery->getWhereClause() . "
      AND sightings.photo='1' ORDER BY CONCAT(trips.Date,sightings.id) DESC LIMIT 1"));
}

if ($request->getSightingID() == "")
{
	die("No photos for " . $pageTitle);
}

$sightingInfo = $request->getSightingInfo();
$speciesInfo = getSpeciesInfo($sightingInfo["species_id"]);
$tripInfo = getTripInfo($sightingInfo["trip_id"]);
$locationInfo = getLocationInfo($sightingInfo["location_id"]);

$nextPhotoID = performCount("Get Next Photo Sighting ID",
	$sightingQuery->getSelectClause() . " " .
	$sightingQuery->getFromClause() . " " .
	$sightingQuery->getWhereClause() . "
      AND sightings.photo='1' AND CONCAT(trips.Date,sightings.id) < '" . $tripInfo["date"] . $sightingInfo["id"] . "'
      ORDER BY CONCAT(trips.Date,sightings.id) DESC LIMIT 1");

$prevPhotoID = performCount("Get Prev Photo Sighting ID",
	$sightingQuery->getSelectClause() . " " .
	$sightingQuery->getFromClause() . " " .
	$sightingQuery->getWhereClause() . "
      AND sightings.photo='1' AND CONCAT(trips.Date,sightings.id) > '" . $tripInfo["date"] . $sightingInfo["id"] . "'
      ORDER BY CONCAT(trips.Date,sightings.id) LIMIT 1");

$tripYear =  substr($tripInfo["date"], 0, 4);

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
<title>birdWalker | <?= $pageTitle ?> Slideshow</title>
</head>

<body style="background-color: white; text-align: center">

<center>
<div style="width: 640px">

<?     $returnHeading = $pageTitle . " Slide Show   <a href=\"" . $originPageScript . "?" . $request->getParams() . "\">&lt;return&gt;</a>";
       browseButtons($returnHeading, "./slideshow.php?" . $request->getParams() . "&origin=" . $originPageScript . "&sightingid=",
					 $sightingInfo["id"],
					 $prevPhotoID, "", $nextPhotoID, ""); ?>

    <div class="pagetitle">
	  <?= $speciesInfo["common_name"] ?>
	</div>

    <div class="pagesubtitle">
	    <?= $sightingInfo["niceDate"] ?>, <?= $locationInfo["name"] ?>
    </div>

<?  $photoFilename = getPhotoFilename($sightingInfo);

	list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

	<img width="<?= $width ?>" height="<?= $height ?>" src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>">
    <div class="copyright">@<?= $tripYear ?> W. F. Walker</div>
</div>
</center>

</html>

