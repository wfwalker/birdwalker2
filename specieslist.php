
<?php

require("./birdwalker.php");

$locationid = $_GET["locationid"];
$year = $_GET["year"];
$month = $_GET["month"];
$county = $_GET["county"];
$state = $_GET["state"];

$speciesListQueryString = "
    SELECT DISTINCT species.CommonName, species.objectid, species.ABACountable
      FROM sighting, species, location, trip
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
      AND location.Name=sighting.LocationName
      AND trip.Date=sighting.TripDate ";

$photoQueryString = "
    SELECT DISTINCT sighting.*
      FROM sighting, species, location, trip
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
      AND location.Name=sighting.LocationName
      AND sighting.Photo='1'
      AND trip.Date=sighting.TripDate ";

$otherWhereClauses = "";
$pageTitle = "";

if ($locationid != "") {
	$otherWhereClauses = $otherWhereClauses . " AND location.objectid=" . $locationid;
	$locationInfo = getLocationInfo($locationid); 
	$pageTitle = $locationInfo["Name"];
} elseif ($county != "") {
	$otherWhereClauses = $otherWhereClauses . " AND location.County='" . $county . "'";
	$pageTitle = $county . " County";
} elseif ($state != "") {
	$otherWhereClauses = $otherWhereClauses . " AND location.State='" . $state . "'";
	$pageTitle = getStateNameForAbbreviation($state);
}

if ($month !="") {
	$otherWhereClauses = $otherWhereClauses . " AND Month(TripDate)=" . $month;
	if ($pageTitle == "") $pageTitle = getMonthNameForNumber($month);
	else $pageTitle = $pageTitle . ", " . getMonthNameForNumber($month);
}
if ($year !="") {
	$otherWhereClauses = $otherWhereClauses . " AND Year(TripDate)=" . $year;
	if ($pageTitle == "") $pageTitle = $year;
	else $pageTitle = $pageTitle . ", " . $year;
}

$speciesListQuery = performQuery($speciesListQueryString . $otherWhereClauses . " ORDER BY species.objectid");
$speciesCount = mysql_num_rows($speciesListQuery);
$divideByTaxo = ($speciesCount > 30);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $pageTitle ?></title>
  </head>
  <body>

<?
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

    <div class=contentright>
      <div class="titleblock">
<?    rightThumbnail($photoQueryString . $otherWhereClauses . " LIMIT 1"); ?>
	  <div class=pagetitle><?= $pageTitle ?></div>
	  <div class=pagesubtitle><?= $pageSubtitle ?></div>
<?    if (($state == 'CA') && ($year != "")) { ?><div class=metadata>See also our <a href="./chronocayearlist.php?year=<?=$year?>">California ABA Year List for <?=$year?></a></div><? } ?>
      </div>

   <div class=heading><?= $speciesCount ?> Species</div>

<? formatTwoColumnSpeciesList($speciesListQuery); ?>

    </div>
  </body>
</html>