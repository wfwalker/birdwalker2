
<?php

require("./speciesquery.php");

$county = param($_GET, "county", "Santa Clara");
$state = param($_GET, "state", "California");

$speciesQuery = new SpeciesQuery;
$speciesQuery->setCounty($county);
$speciesQuery->setState($state);

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
navTrailCounty($state, $county);
?>

    <div class=contentright>
	  <div class="titleblock">
<?      rightThumbnailCounty($county);?>
	  <div class=pagetitle><?= $county ?> County</div>
      <div class=metadata>
<?     countyViewLinks($state, $county); ?>
      </div>
    </div>

    <div class=heading><?= $speciesQuery->getSpeciesCount() ?> species</div>


<? $speciesQuery->formatSpeciesByMonthTable(); ?>

    </div>
  </body>
</html>
