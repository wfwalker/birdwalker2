
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
        locations:
        <a href="./statedetail.php?view=locations&id=<?= $id ?>">list</a> |
	    <a href="./statedetail.php?view=locationsbymonth&id=<?= $id ?>">by month</a> |
	    <a href="./statedetail.php?view=locationsbyyear&id=<?= $id ?>">by year</a><br/>
        species:	
        <a href="./statedetail.php?view=species&id=<?= $id ?>">list</a> |
	    <a href="./statedetail.php?view=speciesbymonth&id=<?= $id ?>">by month</a> |
	    <a href="./statedetail.php?view=speciesbyyear&id=<?= $id ?>">by year</a><br/>
      </div>
      </div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setState($info["Abbreviation"]);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setState($info["Abbreviation"]);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable(); 
}
elseif ($view == 'speciesbymonth')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setState($info["Abbreviation"]);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setState($info['Abbreviation']);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatTwoColumnLocationList();
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setState($info['Abbreviation']);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByYearTable();
}
elseif ($view == 'locationsbymonth')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setState($info['Abbreviation']);
	countHeading($locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByMonthTable();
}
?>

    </div>
  </body>
</html>