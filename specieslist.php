
<?php

require("./birdwalker.php");

$locationid = $_GET["locationid"];
$year = $_GET["year"];
$month = $_GET["month"];
$county = $_GET["county"];
$state = $_GET["state"];

$speciesListQueryString = "SELECT distinct species.* FROM sighting, species, location, trip WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND location.Name=sighting.LocationName AND trip.Date=sighting.TripDate ";

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
	$pageTitle = $pageTitle . ", " . $month;
}
if ($year !="") {
	$speciesListQueryString = $speciesListQueryString . " AND Year(TripDate)=" . $year;
	$pageTitle = $pageTitle . ", " . $year;
}

$speciesListQueryString = $speciesListQueryString . " order by species.objectid;";

$speciesListQuery = performQuery($speciesListQueryString);
$speciesCount = mysql_num_rows($speciesListQuery);
$divideByTaxo = ($speciesCount > 30);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $pageTitle ?></title>
  </head>
  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailBirds(); ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle><?php echo $pageTitle ?></div>
	  <div class=pagesubtitle><?php echo $pageSubtitle ?></div>
      <div class=metadata><?php echo $speciesCount ?> Species</div>
      </div>

<?php

while($info = mysql_fetch_array($speciesListQuery))
{
	$orderNum =  floor($info["objectid"] / pow(10, 9));
	
	if ($divideByTaxo && (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"])))
	{
		$taxoInfo = getBestTaxonomyInfo($info["objectid"]);
		echo "<div class=\"heading\">" . $taxoInfo["CommonName"] . "</div>";
	}
	
	echo "\n<div class=firstcell><a href=\"./speciesdetail.php?id=".$info["objectid"]."\">" . $info["CommonName"] .	"</a></div>";
	
	$prevInfo = $info;
}

?>
    </div>
  </body>
</html>