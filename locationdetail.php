
<?php

require("./birdwalker.php");

$locationID = $_GET['id'];
$siteInfo = getLocationInfo($locationID);
$locationCount = performCount("select count(distinct(objectid)) from location");
$whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName='" . $siteInfo["Name"]. "'";
$siteListQuery = getSpeciesQuery($whereClause);
$siteListCount = getSpeciesCount($whereClause);

$tripQuery = getTripQuery("sighting.LocationName='" . $siteInfo["Name"]. "' and sighting.TripDate=trip.Date");
$tripCount = mysql_num_rows($tripQuery);
$firstSightings = getFirstSightings();

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $siteInfo["Name"] ?></title>
</head>

<body>

<?php navigationHeader(); navigationButtons("./locationdetail.php?id=", $locationID, 1, $locationID - 1, $locationID + 1, $locationCount); ?>

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
if (getEnableEdit()) {
	echo "<div><a href=\"./locationcreate.php?id=" . $locationID . "\">edit</a></div>";
}
?>

</div>

<p class=sighting-notes><?php echo $siteInfo["Notes"] ?></p>

<div class=titleblock>Seen <?php echo $siteListCount ?> species at this location</div>

  <?php
  if ($tripCount < 5)
  {
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
		  echo "<div class=firstcell><a href=\"./tripdetail.php?id=" . $tripInfo["objectid"] . "\">" . $tripInfo["Name"] . " (" . $tripInfo["niceDate"] .  ")</a></div>";
	  }
  }
  else
  {
	  $gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species where sighting.LocationName='" . $siteInfo["Name"] . "' and sighting.SpeciesAbbreviation=species.Abbreviation group by sighting.SpeciesAbbreviation order by speciesid";

	  formatSpeciesByYearTable($gridQueryString, "&locationid=" . $siteInfo["objectid"]);
   }

?>

</div>
</body>
</html>
