
<?php

require("./birdwalker.php");

$county = $_GET["county"];
$state = $_GET["state"];
$locationQuery = performQuery("select * from location where county='" . $county . "' order by State, County, Name");
$locationCount = mysql_num_rows($locationQuery);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $county ?> County</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
$items[]="<a href=\"./statelocations.php?state=" . $state . "\">" . strtolower(getStateNameForAbbreviation($state)) . "</a>";
$items[]=strtolower($county) . " county";
navTrailLocations($items);
pageThumbnail("select *, rand() as shuffle from sighting, location where Photo='1' and sighting.LocationName=location.Name and location.County='" . $county . "' order by shuffle");
?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle><?php echo $county ?> County</div>
        <div class=pagesubtitle><?php echo mysql_num_rows($locationQuery) ?> Locations</div>

      <div class=metadata>
        locations:
        list |
	    <a href="./countylocationsbyyear.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">by year</a>
        species:	
        <a href="./countyspecies.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">list</a> |
	    <a href="./countyspeciesbyyear.php?state=<?= $state ?>&county=<?= urlencode($county) ?>">by year</a>
      </div>
    </div>

   <? formatTwoColumnLocationList($locationQuery, false); ?>

    </div>
  </body>
</html>
