
<?php

require("./birdwalker.php");


$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$locationCount = performCount("select count(distinct location.objectid) from location where State='" . $abbrev . "'");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $stateName ?></title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
$items[] = strtolower($stateName);
navTrailLocations($items);
pageThumbnail("select *, rand() as shuffle from sighting, location where Photo='1' and sighting.LocationName=location.Name and location.State='" . $abbrev . "' order by shuffle");
?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle><?php echo $stateName ?></div>
        <div class=pagesubtitle><?php echo $locationCount ?> Locations</div>
      <div class=metadata>
        locations:
        <a href="./statelocations.php?state=<?php echo $abbrev ?>">list</a> |
	    by year
        species:	
        <a href="./statespecies.php?state=<?php echo $abbrev ?>">list</a> |
	    <a href="./statespeciesbyyear.php?state=<?php echo $abbrev ?>">by year</a>
      </div>

      </div>

<table columns=10 class="report-content" width="100%">

<?
$gridQueryString=" select distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, location where sighting.LocationName=location.Name and State='" . $abbrev . "' group by sighting.LocationName order by location.State, location.County, location.Name;";

formatLocationByYearTable($gridQueryString, "./specieslist.php?");

?>

</table>

    </div>
  </body>
</html>
