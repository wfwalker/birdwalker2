
<?php

require("/Users/walker/Sites/birdwalker/birdwalker.php");

$locationCount = getLocationCount();

?>

<html>
  <head>
    <link title="Style" href="/~walker/birdwalker/stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Locations</title>
  </head>
  <body>

<?php navigationHeader() ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Mary and Bill&apos;s Location List</div>
        <div class=pagesubtitle><?php echo $locationCount ?> Locations</div>
      </div>

<table columns=10 class="report-content" width="100%">

<?
$gridQueryString=" select distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, location where sighting.LocationName=location.Name group by sighting.LocationName order by location.State, location.County, location.Name;";

formatLocationByYearTable($locationCount, $gridQueryString);

?>

</table>

    </div>
  </body>
</html>
