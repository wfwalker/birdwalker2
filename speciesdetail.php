
<?php

require("./birdwalker.php");
require("./sightingquery.php");
require("./locationquery.php");
require("./tripquery.php");

$view = param($_GET, "view", "lists");

$speciesID = param($_GET, 'id', 22330150100);
$speciesInfo = getSpeciesInfo($speciesID);

if ($view != "photo")
{
$tripQuery = new TripQuery;
$tripQuery->setSpeciesID($speciesID);
$tripCount = $tripQuery->getTripCount();

$locationQuery = new LocationQuery;
$locationQuery->setSpeciesID($speciesID);
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
speciesBrowseButtons($speciesID, $view);
navTrailSpecies($speciesID);
?>

  <div class=contentright>
    <div class="pagesubtitle"><?= $speciesInfo["LatinName"] ?></div>
	<div class="titleblock">
<?    if ($view != "photo") { rightThumbnailSpecies($speciesInfo["Abbreviation"]); } ?>
      <div class="pagetitle"><?= $speciesInfo["CommonName"] ?></div>
      <div class=metadata>
<?  if (strlen($speciesInfo["ReferenceURL"]) > 0) { ?>
        <div><a href="<?= $speciesInfo["ReferenceURL"] ?>">See also...</a></div>
<?  }
    if ($speciesInfo["ABACountable"] == '0') { ?>
        <div>NOT ABA COUNTABLE</div>
<?  } ?>
      <a href="./speciesdetail.php?id=<?=$speciesID?>">list</a> |
      <a href="./speciesdetail.php?view=bymonth&id=<?=$speciesID?>">by month</a> |
      <a href="./speciesdetail.php?view=byyear&id=<?=$speciesID?>">by year</a> |
      <a href="./speciesdetail.php?view=photo&id=<?=$speciesID?>">photo</a>

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
	  } elseif ($view == "photo") {
		  $sightingQuery = new SightingQuery;
		  $sightingQuery->setSpeciesID($speciesID);
		  $sightingQuery->formatPhotos();
	  }
?>
   </div>
</body>
</html>
