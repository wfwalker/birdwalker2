
<?php

require("./birdwalker.php");

$state = $_GET["state"];
$locationQuery = performQuery("select * from location where state='" . $state . "' order by State, County, Name");

$randomPhotoSightings = performQuery("select *, rand() as shuffle from sighting, location where Photo='1' and sighting.LocationName=location.Name and location.State='" . $state . "' order by shuffle");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Locations</title>
  </head>
  <body>

<div class=thumb><?php  if (mysql_num_rows($randomPhotoSightings) > 0) { $photoInfo = mysql_fetch_array($randomPhotoSightings); if (mysql_num_rows($randomPhotoSightings) > 0) echo "<td>" . getThumbForSightingInfo($photoInfo) . "</td>"; } ?></div>

<?php
globalMenu();
disabledBrowseButtons();
$items[] = strtolower(getStateNameForAbbreviation($state));
$items[] = "list | <a href=\"./statespeciesbyyear.php?state=" . $state . "\">by year</a>";
navTrailLocations($items);
?>

    <div class=contentright>
      <div class="titleblock">	  
	<div class=pagetitle><?php echo getStateNameForAbbreviation($state) ?></div>
	  <div class=pagesubtitle><?php echo mysql_num_rows($locationQuery) ?> Locations</div>
      </div>

<?php formatTwoColumnLocationList($locationQuery); ?>

    </div>
  </body>
</html>
