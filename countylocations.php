
<?php

require("./birdwalker.php");

$county = $_GET["county"];
$state = $_GET["state"];
$locationQuery = performQuery("select * from location where county='" . $county . "' order by State, County, Name");

$randomPhotoSightings = performQuery("select *, rand() as shuffle from sighting, location where Photo='1' and sighting.LocationName=location.Name and location.County='" . $county . "' order by shuffle");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $county ?> County Locations</title>
  </head>
  <body>

<div class=thumb><?php  if (mysql_num_rows($randomPhotoSightings) > 0) { $photoInfo = mysql_fetch_array($randomPhotoSightings); if (mysql_num_rows($randomPhotoSightings) > 0) echo "<td>" . getThumbForSightingInfo($photoInfo) . "</td>"; } ?></div>

<?php
globalMenu();
disabledBrowseButtons();
$items[]="<a href=\"./statelocations.php?state=" . $state . "\">" . strtolower(getStateNameForAbbreviation($state)) . "</a>";
$items[]=strtolower($county) . " county";
$items[] = "list | <a href=\"./countyspeciesbyyear.php?state=" . $state . "&county=" . urlencode($county) . "\">by year</a>";
navTrailLocations($items); ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle><?php echo $county ?> County</div>
	  <div class=pagesubtitle><?php echo mysql_num_rows($locationQuery) ?> Locations</div>
      </div>

	<?php formatTwoColumnLocationList($locationQuery, false); ?>

    </div>
  </body>
</html>
