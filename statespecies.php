
<?php

require("./birdwalker.php");

$abbrev = $_GET["state"];
$stateName = getStateNameForAbbreviation($abbrev);
$speciesListQuery = performQuery("SELECT distinct species.* FROM sighting, species, location  WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND location.Name=sighting.LocationName AND location.State='" . $abbrev . "' order by species.objectid;");

$speciesCount = mysql_num_rows($speciesListQuery);
$divideByTaxo = ($speciesCount > 30);

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
navTrailLocations(strtolower($stateName));
pageThumbnail("select sighting.*, rand() as shuffle from sighting, location where sighting.Photo='1' and sighting.LocationName=location.Name and location.State='" . $abbrev . "' order by shuffle");
 ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle><?= $stateName ?></div>
      <div class=pagesubtitle><?= $speciesCount ?> Species</div>
      <div class=metadata>
        <? stateViewLinks($abbrev) ?>
      </div>
      </div>

<?php

formatTwoColumnSpeciesList($speciesListQuery);
 
?>
    </div>
  </body>
</html>