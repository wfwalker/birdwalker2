
<?php

require("./birdwalker.php");

$county = param($_GET, "county", "San Mateo");
$state = param($_GET, "state", "CA");

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
formatLocationByMonthTable(
    "WHERE sighting.LocationName=location.Name and County='" . $county . "' and State='" . $abbrev . "'",
    "./specieslist.php?",
    false);
?>

</table>

    </div>
  </body>
</html>
