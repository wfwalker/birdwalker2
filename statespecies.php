
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
navTrailLocations();
 ?>

    <div class=contentright>
      <div class="titleblock">	  
<?    rightThumbnailState("$abbrev"); ?>
	  <div class=pagetitle><?= $stateName ?></div>
      <div class=metadata>
        <? stateViewLinks($abbrev) ?>
      </div>
      </div>

 <div class=heading><?= $speciesCount ?> Species</div>

<? formatTwoColumnSpeciesList($speciesListQuery); ?>

    </div>
  </body>
</html>