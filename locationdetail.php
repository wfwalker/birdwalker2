
<?php

require("./birdwalker.php");

$siteInfo = getLocationInfo($_GET['id']);
$locationCount = performCount("select count(distinct(objectid)) from location");
$whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName='" . $siteInfo["Name"]. "'";
$siteListQuery = getSpeciesQuery($whereClause);
$siteListCount = getSpeciesCount($whereClause);

$tripQuery = getTripQuery("sighting.LocationName='" . $siteInfo["Name"]. "' and sighting.TripDate=trip.Date");
$tripCount = getTripCount("sighting.LocationName='" . $siteInfo["Name"]. "' and sighting.TripDate=trip.Date");
$firstSightings = getFirstSightings();

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $siteInfo["Name"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

    <div class="navigationleft">
	  <a href="./locationdetail.php?id=1">first</a>
	  <a href="./locationdetail.php?id=<?php echo $_GET['id'] - 1 ?>">prev</a>
      <a href="./locationdetail.php?id=<?php echo $_GET['id'] + 1 ?>">next</a>
      <a href="./locationdetail.php?id=<?php echo $locationCount ?>">last</a>
    </div>

<div class="contentright">
  <div class="titleblock">
  <div class=pagetitle><?php echo $siteInfo["Name"] ?></div>
  <div class=pagesubtitle>
    <a href="./countydetail.php?county=<?php echo $siteInfo["County"] ?>"><?php echo $siteInfo["County"] ?> County</a>,
    <a href="./statedetail.php?state=<?php echo $siteInfo["State"] ?>"><?php echo getStateNameForAbbreviation($siteInfo["State"]) ?></a>
  </div>

<?php
if (strlen($siteInfo["ReferenceURL"]) > 0) {
	echo "<div><a href=\"" . $siteInfo["ReferenceURL"] . "\">See also...</a></div>";
}
?>

</div>

<p class=sighting-notes><?php echo $siteInfo["Notes"] ?></p>

  <?php
  if ($tripCount < 5)
  {
	  echo "
	  <div class=\"titleblock\">Seen ". $siteListCount . " species at this location</div>";

	  $divideByTaxo = ($siteListCount > 30);
	
	  while($info = mysql_fetch_array($siteListQuery))
	  {
		  $orderNum =  floor($info["objectid"] / pow(10, 9));
		
		  if ($divideByTaxo && (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"])))
		  {
			  $taxoInfo = getBestTaxonomyInfo($info["objectid"]);
			  echo "<div class=\"titleblock\">" . $taxoInfo["CommonName"] . "</div>";
		  }

		  echo "<div class=firstcell><a href=\"./speciesdetail.php?id=".$info["objectid"]."\">".$info["CommonName"]."</a></div>";
		
		  $prevInfo = $info;
	  }

	  echo "<div class=\"titleblock\">Visited on " . $tripCount . " trips</div>";

	  // list the trips that included this location
	  while($tripInfo = mysql_fetch_array($tripQuery)) {
		  echo "<div class=firstcell><a href=\"./tripdetail.php?id=" . $tripInfo["objectid"] . "\">" . $tripInfo["Name"] . " (" . $tripInfo["Date"] .  ")</a></div>";
	  }
  }
  else
  {
	  $gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species where sighting.LocationName='" . $siteInfo["Name"] . "' and sighting.SpeciesAbbreviation=species.Abbreviation group by sighting.SpeciesAbbreviation order by speciesid";

	  formatSpeciesByYearTable($siteListCount, $gridQueryString);
   }

?>

</div>
</body>
</html>
