
<?php

require("./birdwalker.php");
require("./sightingquery.php");
require("./map.php");
require("./tripquery.php");

$view = param($_GET, "view", "lists");

$speciesID = param($_GET, 'speciesid', 22330150100);
$speciesInfo = getSpeciesInfo($speciesID);

$locationQuery = new LocationQuery;
$locationQuery->setSpeciesID($speciesID);
$extrema = $locationQuery->findExtrema();

if ($view != "photo")
{
	$tripQuery = new TripQuery;
	$tripQuery->setSpeciesID($speciesID);
	$tripCount = $tripQuery->getTripCount();
	
	$locationCount = $locationQuery->getLocationCount();
}

?>

<html>

<head>
  <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet"/>
  <title>birdWalker | <?= $speciesInfo["CommonName"] ?></title>
</head>

<body>

<?php
globalMenu();
speciesBrowseButtons("./speciesdetail.php", $speciesID, $view);
navTrailSpecies($speciesID);
?>

  <div class=contentright>
    <div class="pagesubtitle"><?= $speciesInfo["LatinName"] ?></div>
	<div class="titleblock">
<?    if ($view != "photo") { rightThumbnailSpecies($speciesInfo["Abbreviation"]); } ?>
      <div class="pagetitle">
        <?= $speciesInfo["CommonName"] ?>
        <?= editlink("./speciesedit.php?speciesid=" . $speciesID) ?>
      </div>
      <div class=metadata>
<?  if (strlen($speciesInfo["ReferenceURL"]) > 0) { ?>
        <div><a href="<?= $speciesInfo["ReferenceURL"] ?>">See also...</a></div>
<?  }
    if ($speciesInfo["ABACountable"] == '0') { ?>
        <div>NOT ABA COUNTABLE</div>
<?  } ?>
      <a href="./speciesdetail.php?speciesid=<?=$speciesID?>">list</a> |
      <a href="./speciesdetail.php?view=bymonth&speciesid=<?=$speciesID?>">by month</a> |
      <a href="./speciesdetail.php?view=byyear&speciesid=<?=$speciesID?>">by year</a> |
      <a href="./speciesdetail.php?view=photo&speciesid=<?=$speciesID?>">photo</a> |
      <a href="./speciesdetail.php?view=map&speciesid=<?=$speciesID?>">map</a><br/>

      </div>
   </div>

   <div class=sighting-notes><?= $speciesInfo["Notes"] ?></div>

<?
	
	  if ($view == "lists") {
		  countHeading($tripCount, "trip");
		  $tripQuery->formatTwoColumnTripList();
		  countHeading($locationCount, "location");
		  $locationQuery->formatTwoColumnLocationList();
	  } elseif ($view == "bymonth") {
		  doubleCountHeading($tripCount, "trip", $locationCount, "location");
		  $locationQuery->formatLocationByMonthTable();
	  } elseif ($view == "byyear") {
		  doubleCountHeading($tripCount, "trip", $locationCount, "location");
		  $locationQuery->formatLocationByYearTable();
	  } else if ($view == "map") {
		  countHeading($locationCount, "location");
		  $map = new Map("./speciesdetail.php");
		  //		echo "<br clear=\"all\">";
		  $map->setFromRequest($_GET);
		  $map->draw();
	  } elseif ($view == "photo") {
		  $sightingQuery = new SightingQuery;
		  $sightingQuery->setSpeciesID($speciesID);
		  $sightingQuery->formatPhotos();
	  }
?>
   </div>
</body>
</html>
