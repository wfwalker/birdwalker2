
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
	  <div class=pagesubtitle><?php echo mysql_num_rows($locationQuery) ?> Locations</div>
      <div class=metadata>
        locations:
        list |
	    <a href="./statelocationsbyyear.php?state=<?php echo $abbrev ?>">by year</a>
        species:	
        <a href="./statespecies.php?state=<?php echo $abbrev ?>">list</a> |
	    <a href="./statespeciesbyyear.php?state=<?php echo $abbrev ?>">by year</a>
      </div>


      </div>

<?php formatTwoColumnLocationList($locationQuery); ?>

    </div>
  </body>
</html>
