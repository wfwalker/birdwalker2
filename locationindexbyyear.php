
<?php

require("./birdwalker.php");

$locationCount = performCount("select count(distinct location.objectid) from location");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Locations by Year</title>
  </head>
  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailLocations("&gt; <a href=\"./locationindex.php\">list<a/> | by year"); ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Locations by Year</div>
        <div class=pagesubtitle><?php echo $locationCount ?> Locations</div>
      </div>

<table columns=10 class="report-content" width="100%">

<?
$gridQueryString=" select distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, location where sighting.LocationName=location.Name group by sighting.LocationName order by location.State, location.County, location.Name;";

formatLocationByYearTable($gridQueryString, "");

?>

</table>

    </div>
  </body>
</html>