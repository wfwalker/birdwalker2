
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./chronolist.php");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);

htmlHead("Species");
globalMenu();
navTrail();

?>

    <div class=contentright>
	  <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
<?    rightThumbnailAll(); ?>
	  <div class=pagetitle>Species</div>
	    <div class=metadata>
          <?= $request->linkToSelfChangeView("list", "list") ?> |
          <?= $request->linkToSelfChangeView("chrono", "ABA") ?> |
          <?= $request->linkToSelfChangeView("speciesbymonth", "by month") ?> |
          <?= $request->linkToSelfChangeView("speciesbyyear", "by year") ?><br/>
        </div>
      </div>

<?

if ($request->getView() == "" || $request->getView() == "list") {
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 
} else if ($request->getView() == "speciesbymonth") {
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable($speciesQuery);
} else if ($request->getView() == "speciesbyyear") {
	countHeading($speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable($speciesQuery);
} else if ($request->getView() == "chrono") {
	$chrono = new ChronoList($request);
	$chrono->draw();
}

footer();

?>

    </div>

<?
htmlFoot();
?>
