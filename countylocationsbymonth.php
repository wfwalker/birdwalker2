
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
navTrailCounty($abbrev, $county);
?>

    <div class=contentright>
      <div class="titleblock">	  
<?      rightThumbnailCounty($county) ?>
        <div class=pagetitle><?= $county?> County</div>

      <div class=metadata>
<?       countyViewLinks($abbrev, $county); ?>
      </div>

    </div>

<div class=heading><?= mysql_num_rows($locationQuery) ?> Locations</div>

<table columns=10 class="report-content" width="100%">

<?
$gridQueryString="select distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << month(TripDate)) as mask from sighting, location where sighting.LocationName=location.Name and County='" . $county . "' and State='" . $abbrev . "' group by sighting.LocationName order by location.State, location.County, location.Name;";

formatLocationByMonthTable($gridQueryString, "./specieslist.php?", false);

?>

</table>

    </div>
  </body>
</html>
