
<?php

require("./birdwalker.php");

$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$locationQuery = performQuery("select * from location where state='" . $abbrev . "' order by State, County, Name");

$randomPhotoSightings = performQuery("select *, rand() as shuffle from sighting, location where Photo='1' and sighting.LocationName=location.Name and location.State='" . $abbrev . "' order by shuffle");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $stateName ?></title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
$items[] = strtolower($stateName);
navTrailLocations($items);
?>

    <div class=contentright>
      <div class="titleblock">	  
<?    rightThumbnailState("$abbrev"); ?>
	<div class=pagetitle><?= $stateName ?></div>
	  <div class=pagesubtitle><?= mysql_num_rows($locationQuery) ?> Locations</div>
      <div class=metadata>
        <? stateViewLinks($abbrev) ?>
      </div>


      </div>

<?php formatTwoColumnLocationList($locationQuery); ?>

    </div>
  </body>
</html>
