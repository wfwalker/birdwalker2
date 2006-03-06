
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./sightingquery.php");

$request = new Request;
$sightingQuery = new SightingQuery($request);
$pageTitle =  $sightingQuery->getPageTitle();

if ($request->getSightingID() == "")
{ 
	$request->setSightingID(performCount("Get First Photo Sighting ID",
	$sightingQuery->getSelectClause() . " " .
	$sightingQuery->getFromClause() . " " .
	$sightingQuery->getWhereClause() . "
      AND Photo='1' ORDER BY CONCAT(TripDate,sighting.objectid) DESC LIMIT 1"));
}

if ($request->getSightingID() == "")
{
	die("No photos for " . $pageTitle);
}

$sightingInfo = $request->getSightingInfo();

$speciesInfo = performOneRowQuery("Get Species Info", "SELECT * FROM species WHERE Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");

$nextPhotoID = performCount("Get Next Photo Sighting ID",
	$sightingQuery->getSelectClause() . " " .
	$sightingQuery->getFromClause() . " " .
	$sightingQuery->getWhereClause() . "
      AND Photo='1' AND CONCAT(TripDate,sighting.objectid) < '" . $sightingInfo["TripDate"] . $sightingInfo["objectid"] . "'
      ORDER BY CONCAT(TripDate,sighting.objectid) DESC LIMIT 1");

$prevPhotoID = performCount("Get Prev Photo Sighting ID",
	$sightingQuery->getSelectClause() . " " .
	$sightingQuery->getFromClause() . " " .
	$sightingQuery->getWhereClause() . "
      AND Photo='1' AND CONCAT(TripDate,sighting.objectid) > '" . $sightingInfo["TripDate"] . $sightingInfo["objectid"] . "'
      ORDER BY CONCAT(TripDate,sighting.objectid) LIMIT 1");

$tripYear =  substr($sightingInfo["TripDate"], 0, 4);

?>

<html>

<head>
<META HTTP-EQUIV="Refresh" CONTENT="10; URL=./slideshow.php?<?= $request->getParams() ?>&sightingid=<?= $nextPhotoID ?>">
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
<title>birdWalker | <?= $pageTitle ?> Slideshow</title>
</head>

<body>

<?= $request->globalMenu(); ?>

  <div class="topright-photo">
	<? browseButtons($pageTitle . " Slide Show", "./slideshow.php?" . $request->getParams() . "&sightingid=",
					 $sightingInfo["objectid"],
					 $prevPhotoID, "", $nextPhotoID, ""); ?>

    <div class="pagetitle"><?= $speciesInfo["CommonName"] ?></div>
    <div class="pagesubtitle">
	    <?= $sightingInfo["niceDate"] ?>, <?= $sightingInfo["LocationName"] ?>
    </div>
  </div>

  <div class="contentright">
<?      $photoFilename = getPhotoFilename($sightingInfo);

	    list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

    <center>
	    <img width=<?= $width ?> height=<?= $height ?> src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>">
        <div class="copyright">@<?= $tripYear ?> W. F. Walker</div>
    </center>
  </div>

</html>