
<?php

require("./birdwalker.php");

$county = $_GET["county"];
$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$locationQuery = performQuery("select * from location where county='" . $county . "' order by State, County, Name");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?echo $county ?> County</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
$items[] = "<a href=\"./statelocationsbyyear.php?state=" .  $abbrev . "\">" . strtolower($stateName) . "</a>";
$items[] = strtolower($county . " county");
navTrailLocations($items);
pageThumbnail("select *, rand() as shuffle from sighting, location where Photo='1' and sighting.LocationName=location.Name and location.State='" . $abbrev . "' order by shuffle");
?>

    <div class=contentright>
      <div class="titleblock">	  
	<div class=pagetitle><?= $county?> County</div>
	<div class=pagesubtitle><?= mysql_num_rows($locationQuery) ?> Locations</div>

      <div class=metadata>
        locations:
        <a href="./countylocations.php?state=<?= $abbrev ?>&county=<?= urlencode($county) ?>">list</a> |
	    by year
        species:	
        <a href="./countyspecies.php?state=<?= $abbrev ?>&county=<?= urlencode($county) ?>">list</a> |
	    <a href="./countyspeciesbyyear.php?state=<?= $abbrev ?>&county=<?= urlencode($county) ?>">by year</a>
      </div>

    </div>

<table columns=10 class="report-content" width="100%">

<?
$gridQueryString="select distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, location where sighting.LocationName=location.Name and County='" . $county . "' and State='" . $abbrev . "' group by sighting.LocationName order by location.State, location.County, location.Name;";

formatLocationByYearTable($gridQueryString, "./specieslist.php?");

?>

</table>

    </div>
  </body>
</html>
