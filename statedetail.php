
<?php

require("./birdwalker.php");
require("./speciesquery.php");
require("./locationquery.php");

$id = param($_GET, "id", 3);
$view = param($_GET, "view", "species");

$info = getStateInfo($id);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $info["Name"] ?></title>
  </head>
  <body>

<?php
globalMenu();
stateBrowseButtons($id, $view);
navTrailLocations();
?>

    <div class=contentright>
      <div class="titleblock">	  
<?    rightThumbnailState($info["Abbreviation"]); ?>
	  <div class=pagetitle><?= $info["Name"] ?></div>
      <div class=metadata>
<?    stateViewLinks($id) ?>
      </div>
      </div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setState($info["Abbreviation"]); ?>

    <div class=heading><?= $speciesQuery->getSpeciesCount() ?> Species</div>
<?  $speciesQuery->formatTwoColumnSpeciesList(); 
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setState($info["Abbreviation"]); ?>

    <div class=heading><?= $speciesQuery->getSpeciesCount() ?> Species</div>
<?  $speciesQuery->formatSpeciesByYearTable(); 
}
elseif ($view == 'speciesbymonth')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setState($info["Abbreviation"]); ?>

    <div class=heading><?= $speciesQuery->getSpeciesCount() ?> Species</div>
<?  $speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setState($info['Abbreviation']); ?>

    <div class=heading><?= $locationQuery->getLocationCount() ?> Locations</div>
<?  $locationQuery->formatTwoColumnLocationList();
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setState($info['Abbreviation']); ?>

    <div class=heading><?= $locationQuery->getLocationCount() ?> Locations</div>
<?  $locationQuery->formatLocationByYearTable();
}
elseif ($view == 'locationsbymonth')
{ ?>
    $locationQuery = new LocationQuery;
	$locationQuery->setState($info['Abbreviation']); ?>

    <div class=heading><?= $locationQuery->getLocationCount() ?> Locations</div>
<?  $locationQuery->formatLocationByMonthTable();
}
?>

    </div>
  </body>
</html>