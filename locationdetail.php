
<?php

require("./birdwalker.php");

$locationID = $_GET['id'];
$siteInfo = getLocationInfo($locationID);
$locationCount = performCount("select count(distinct(objectid)) from location");
$speciesCount = performCount("select count(distinct species.objectid) from species, sighting where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName='" . $siteInfo["Name"]. "'");

$tripQuery = performQuery("select distinct trip.objectid, trip.*, date_format(Date, '%M %e, %Y') as niceDate, count(distinct sighting.SpeciesAbbreviation) as tripCount from trip, sighting where sighting.LocationName='" . $siteInfo["Name"]. "' and sighting.TripDate=trip.Date group by trip.Date order by trip.Date desc");

$tripCount = mysql_num_rows($tripQuery);
// $firstSightings = getFirstSightings(); // NOT CURRENTLY IN USE

$randomPhotoSightings = performQuery("select *, rand() as shuffle from sighting where Photo='1' and LocationName='" . $siteInfo["Name"] . "' order by shuffle");

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $siteInfo["Name"] ?></title>
</head>

<body>

<?php
globalMenu();
browseButtons("./locationdetail.php?id=", $locationID, 1, $locationID - 1, $locationID + 1, $locationCount);
$items[] = "<a href=\"./statelocations.php?state=" .  $siteInfo["State"] . "\">" . strtolower(getStateNameForAbbreviation($siteInfo["State"])) . "</a>";
$items[] = "<a href=\"./countylocations.php?county=" . $siteInfo["County"] . "&state=" . $siteInfo["State"] . "\">" . strtolower($siteInfo["County"]) . " county</a>";
$items[] = strtolower($siteInfo["Name"]);
navTrailLocations($items);
?>

<div class=thumb><?php  if (mysql_num_rows($randomPhotoSightings) > 0) { $photoInfo = mysql_fetch_array($randomPhotoSightings); if (mysql_num_rows($randomPhotoSightings) > 0) echo "<td>" . getThumbForSightingInfo($photoInfo) . "</td>"; } ?></div>

<div class="contentright">
  <div class="titleblock">
    <div class=pagetitle><?php echo $siteInfo["Name"] ?></div>

<?php
if (strlen($siteInfo["ReferenceURL"]) > 0) {
	echo "<div><a href=\"" . $siteInfo["ReferenceURL"] . "\">See also...</a></div>";
}
if (getEnableEdit()) {
	echo "<div><a href=\"./locationcreate.php?id=" . $locationID . "\">edit</a></div>";
}
if (strlen($siteInfo["Latitude"]) > 0) {
	echo "<div><a href=\"http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude=" . $siteInfo["Latitude"] . "&longitude=-" . $siteInfo["Longitude"] . "\">Map...</a></div>";
}
?>

  </div>

<p class=sighting-notes><?php echo $siteInfo["Notes"] ?></p>

<?php
  if ($tripCount < 5)
  {
	  // PART ONE, TRIPS
	  echo "<div class=\"heading\">Visited on " . $tripCount . " trips</div>";

	  while($tripInfo = mysql_fetch_array($tripQuery))
	  {
		  echo "<div class=firstcell><a href=\"./tripdetail.php?id=" . $tripInfo["objectid"] . "\">" . $tripInfo["Name"] . " (" . $tripInfo["niceDate"] .  ")</a></div>";
	  }

	  echo "<div class=heading>Observed " . $speciesCount . " species at this location</div>";

	  formatTwoColumnSpeciesList(performQuery("select distinct(species.objectid), species.* from species, sighting where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName='" . $siteInfo["Name"]. "' order by species.objectid"));
  }
  else
  {
	  $gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species where sighting.LocationName='" . $siteInfo["Name"] . "' and sighting.SpeciesAbbreviation=species.Abbreviation group by sighting.SpeciesAbbreviation order by speciesid";

	  echo "<div class=heading>Observed " . $speciesCount . " species at this location on " . $tripCount . " trips</div>";

	  formatSpeciesByYearTable($gridQueryString, "&locationid=" . $siteInfo["objectid"]);
  }
?>

</div>
</body>
</html>
