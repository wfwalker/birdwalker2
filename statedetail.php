
<?php

require("./birdwalker.php");

$id = $_GET["id"];
$view = $_GET["view"];
if ($view == "") $view = "species";
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
    $speciesListQuery = performQuery("
	    SELECT distinct species.* FROM sighting, species, location
		    WHERE species.Abbreviation=sighting.SpeciesAbbreviation
			    AND location.Name=sighting.LocationName
			    AND location.State='" . $info["Abbreviation"] . "'
			    ORDER BY species.objectid;"); ?>

    <div class=heading><?= mysql_num_rows($speciesListQuery) ?> Species</div>
<?  formatTwoColumnSpeciesList($speciesListQuery);
}
elseif ($view == 'speciesbyyear')
{
    $stateListCount =  performCount("
        SELECT COUNT(DISTINCT species.objectid)
          FROM species, sighting, location
          WHERE species.Abbreviation=sighting.SpeciesAbbreviation
            AND sighting.LocationName=location.Name AND location.State='" . $info["Abbreviation"] . "'");
    $annualStateTotal = performQuery("
        SELECT COUNT(DISTINCT species.objectid) AS count, year(sighting.TripDate) AS year
          FROM sighting, species, location
          WHERE species.Abbreviation=sighting.SpeciesAbbreviation
            AND sighting.LocationName=location.Name AND location.State='" . $info["Abbreviation"] . "'
          GROUP BY year"); ?>
		 <div class=heading><?= $stateListCount ?> Species</div>
<?  formatSpeciesByYearTable(
        "WHERE sighting.SpeciesAbbreviation=species.Abbreviation
            AND sighting.LocationName=location.Name AND location.State='". $info["Abbreviation"] . "'",
            "&state=" . $abbrev,
            $annualStateTotal);

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