
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./chronolist.php");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);
$view = param($_GET, "view", "");

htmlHead("Species");

globalMenu();
navTrailBirds();
?>

    <div class=contentright>
	  <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
<?    rightThumbnailAll(); ?>
	  <div class=pagetitle>Species</div>
	    <div class=metadata>
          <a href="./speciesindex.php">list</a> |
          <a href="./speciesindex.php?view=chrono">ABA</a> |
          <a href="./speciesindex.php?view=bymonth">by month</a> |
          <a href="./speciesindex.php?view=byyear">by year</a>
        </div>
      </div>

<?

if ($view == "") {
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 
} else if ($view == "bymonth") {
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable($speciesQuery);
} else if ($view == "byyear") {
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable($speciesQuery);
} else if ($view == "chrono") {
	$chrono = new ChronoList($request);
	$chrono->draw();
}

footer();

?>

    </div>

<?
htmlFoot();
?>
