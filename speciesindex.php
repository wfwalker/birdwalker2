
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");

$speciesQuery = new SpeciesQuery;
$view = param($_GET, "view", "");

?>

<html>

  <? htmlHead("Species"); ?>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

    <div class=contentright>
      <div class="titleblock">	  
<?    rightThumbnailAll(); ?>
	  <div class=pagetitle>Species</div>
	    <div class=metadata>
          <a href="./speciesindex.php">list</a> |
          <a href="./speciesindex.php?view=bymonth">by month</a> |
          <a href="./speciesindex.php?view=byyear">by year</a>
        </div>
      </div>

<?

countHeading($speciesQuery->getSpeciesCount(), "species");

if ($view == "") {
	$speciesQuery->formatTwoColumnSpeciesList(); 
} else if ($view == "bymonth") {
	$speciesQuery->formatSpeciesByMonthTable($speciesQuery);
} else if ($view == "byyear") {
	$speciesQuery->formatSpeciesByYearTable($speciesQuery);
}

?>

    </div>
  </body>
</html>
