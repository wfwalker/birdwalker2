
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
navTrailCounty($abbrev, $county);
 ?>

    <div class=contentright>
      <div class="titleblock">	  
<?      rightThumbnailCounty($county); ?>
        <div class=pagetitle><?= $county ?> County</div>

      <div class=metadata>
<?        countyViewLinks($abbrev, $county); ?>
      </div>

      </div>

<div class=heading><?= $speciesCount ?> Species</div>

<?php

formatTwoColumnSpeciesList($speciesListQuery);
 
?>
    </div>
  </body>
</html>