
<?php

require("./speciesquery.php");

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
    $locationQuery = performQuery("
        SELECT * FROM location WHERE state='" . $info["Abbreviation"] . "' ORDER BY State, County, Name"); ?>

    <div class=heading><?= mysql_num_rows($locationQuery) ?> Locations</div>
<?  formatTwoColumnLocationList($locationQuery);
}
elseif ($view == 'locationsbyyear')
{ ?>
    <div class=heading>
        <?= performCount("SELECT COUNT(DISTINCT objectid) from location where state='" . $info["Abbreviation"] . "'") ?> Locations
    </div>
<?  formatLocationByYearTable("
        WHERE sighting.LocationName=location.Name
        AND State='" . $info["Abbreviation"] . "'", "./specieslist.php?");
}
elseif ($view == 'locationsbymonth')
{ ?>
    <div class=heading>
        <?= performCount("SELECT COUNT(DISTINCT objectid) from location where state='" . $info["Abbreviation"] . "'") ?> Locations
    </div>

<?  formatLocationByMonthTable("
        WHERE sighting.LocationName=location.Name
        AND State='" . $info["Abbreviation"] . "'", "./specieslist.php?");
}
?>

    </div>
  </body>
</html>