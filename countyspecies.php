
<?php

require("./birdwalker.php");

$county = $_GET["county"];
$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$speciesListQuery = performQuery("SELECT distinct species.* FROM sighting, species, location  WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND location.Name=sighting.LocationName AND location.County='" . $county . "' and location.State='" . $abbrev . "' order by species.objectid;");

$speciesCount = mysql_num_rows($speciesListQuery);
$divideByTaxo = ($speciesCount > 30);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $county ?> County</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
$items[]="<a href=\"./statespecies.php?state=" . $abbrev . "\">" . strtolower($stateName) . "</a>";
navTrailLocations($items);
pageThumbnail("select sighting.*, rand() as shuffle from sighting, location where sighting.Photo='1' and sighting.LocationName=location.Name and location.State='" . $abbrev . "' order by shuffle");
 ?>

    <div class=contentright>
      <div class="titleblock">	  
	<div class=pagetitle><?= $county ?> County</div>
      <div class=pagesubtitle><?= $speciesCount ?> Species</div>

      <div class=metadata>
        locations:
        <a href="./countylocations.php?state=<?= $abbrev ?>&county=<?= urlencode($county) ?>">list</a> |
	    <a href="./countylocationsbyyear.php?state=<?= $abbrev ?>&county=<?= urlencode($county) ?>">by year</a>
        species:	
        list |
	    <a href="./countyspeciesbyyear.php?state=<?= $abbrev ?>&county=<?= urlencode($county) ?>">by year</a>
      </div>

      </div>

<?php

formatTwoColumnSpeciesList($speciesListQuery);
 
?>
    </div>
  </body>
</html>