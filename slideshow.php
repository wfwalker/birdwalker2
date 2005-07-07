
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./sightingquery.php");

$request = new Request;

$sightingQuery = new SightingQuery($request);

$randomPhotoSightings = performQuery("
    SELECT *, date_format(TripDate, '%W,  %M %e, %Y') as niceDate, rand() AS shuffle ".
	  $sightingQuery->getFromClause() . " " . 
	  $sightingQuery->getWhereClause() .  " AND sighting.Photo='1'
      ORDER BY shuffle
      LIMIT 1");

$sightingInfo = mysql_fetch_array($randomPhotoSightings);
$tripYear =  substr($sightingInfo["TripDate"], 0, 4);
?>

<html>

<head>
<META HTTP-EQUIV=Refresh CONTENT="10; URL=./slideshow.php?<?= $request->getParams() ?>">
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
<title>birdWalker | Slideshow</title>
</head>

<body>

<?php
$items[] = "slide show";
navTrail($items);
?>

<div class=contentright>

	<div class=pagesubtitle><?= $sightingInfo["niceDate"] ?>, <?= $sightingInfo["LocationName"] ?></div>
<div class=titleblock>
<div class=pagetitle><?= $sightingInfo["CommonName"] ?></div>
</div>

<div>
<?      $photoFilename = getPhotoFilename($sightingInfo);

	    list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

	    <img width=<?= $width ?> height=<?= $height ?> src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>">
        <div class=copyright>@<?= $tripYear ?> W. F. Walker</div>
</div>

<?
footer();
?>

</div>

<?
htmlFoot();
?>
