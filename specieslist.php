
<?php

require("./birdwalker.php");

$locationid = $_GET["locationid"];
$year = $_GET["year"];
$month = $_GET["month"];
$county = $_GET["county"];
$state = $_GET["state"];

$speciesListQueryString = "SELECT distinct species.* FROM sighting, species, location, trip WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND location.Name=sighting.LocationName AND trip.Date=sighting.TripDate ";

$pageTitle = "";

if ($locationid != "") {
	$speciesListQueryString = $speciesListQueryString . " AND location.objectid=" . $locationid;
	$locationInfo = getLocationInfo($locationid); 
	$pageTitle = $locationInfo["Name"];
} elseif ($county != "") {
	$speciesListQueryString = $speciesListQueryString . " AND location.County='" . $county . "'";
	$pageTitle = $county . " County";
} elseif ($state != "") {
	$speciesListQueryString = $speciesListQueryString . " AND location.State='" . $state . "'";
	$pageTitle = getStateNameForAbbreviation($state);
}

if ($month !="") {
	$speciesListQueryString = $speciesListQueryString . " AND Month(TripDate)=" . $month;
	if ($pageTitle == "") $pageTitle = $month;
	else $pageTitle = $pageTitle . ", " . $month;
}
if ($year !="") {
	$speciesListQueryString = $speciesListQueryString . " AND Year(TripDate)=" . $year;
	if ($pageTitle == "") $pageTitle = $year;
	else $pageTitle = $pageTitle . ", " . $year;
}

$speciesListQueryString = $speciesListQueryString . " order by species.objectid;";

$speciesListQuery = performQuery($speciesListQueryString);
$speciesCount = mysql_num_rows($speciesListQuery);
$divideByTaxo = ($speciesCount > 30);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $pageTitle ?></title>
  </head>
  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailBirds(); ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle><?= $pageTitle ?></div>
	  <div class=pagesubtitle><?= $pageSubtitle ?></div>
      <div class=metadata><?= $speciesCount ?> Species</div>
      </div>

<?php

formatTwoColumnSpeciesList($speciesListQuery);
 
?>
    </div>
  </body>
</html>