
<?php

require("./birdwalker.php");

$locationID = $_GET['id'];
$siteInfo = getLocationInfo($locationID);
$locationCount = performCount("select count(distinct(objectid)) from location");
$speciesCount = performCount("select count(distinct species.objectid) from species, sighting where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName='" . $siteInfo["Name"]. "'");

$tripQuery = performQuery("select distinct trip.objectid, trip.*, date_format(Date, '%M %e, %Y') as niceDate, count(distinct sighting.SpeciesAbbreviation) as tripCount from trip, sighting where sighting.LocationName='" . $siteInfo["Name"]. "' and sighting.TripDate=trip.Date group by trip.Date order by trip.Date desc");

$tripCount = mysql_num_rows($tripQuery);


?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $siteInfo["Name"] ?></title>
</head>

<body>

<?php
globalMenu();
browseButtons("./locationdetail.php?id=", $locationID, 1, $locationID - 1, $locationID + 1, $locationCount);
$items[] = "<a href=\"./statelocations.php?state=" .  $siteInfo["State"] . "\">" . strtolower(getStateNameForAbbreviation($siteInfo["State"])) . "</a>";
$items[] = "<a href=\"./countylocations.php?county=" . $siteInfo["County"] . "&state=" . $siteInfo["State"] . "\">" . strtolower($siteInfo["County"]) . " county</a>";
$items[] = strtolower($siteInfo["Name"]);
navTrailLocations($items);
pageThumbnail("select *, rand() as shuffle from sighting where Photo='1' and LocationName='" . $siteInfo["Name"] . "' order by shuffle");
?>

<div class="contentright">
  <div class="titleblock">
    <div class=pagetitle><?= $siteInfo["Name"] ?></div>

<?php
if (strlen($siteInfo["ReferenceURL"]) > 0) {
?>
	<div><a href="<?= $siteInfo["ReferenceURL"] ?>">See also...</a></div>
<?
}
if (getEnableEdit()) {
?>
	<div><a href="./locationcreate.php?id=<?= $locationID ?>">edit</a></div>
<?
}
if (strlen($siteInfo["Latitude"]) > 0) {
?>
	<div><a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude=<?= $siteInfo["Latitude"] ?>&longitude=-<?= $siteInfo["Longitude"] ?>">Map...</a></div>
<?
}
?>

    </div>

<p class=sighting-notes><?= $siteInfo["Notes"] ?></p>

<?php
  if ($tripCount < 5) // PART ONE, TRIPS
  {
?>
    <div class="heading">Visited on <?= $tripCount ?> trips</div>
<?
    while($tripInfo = mysql_fetch_array($tripQuery))
    {
?>
    <div class=firstcell><a href="./tripdetail.php?id=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["Name"] ?> (<?= $tripInfo["niceDate"] ?>)</a></div>
<?
	  }
?>
	<div class=heading>Observed <?= $speciesCount ?> species at this location</div>
<?
    formatTwoColumnSpeciesList(performQuery("select distinct(species.objectid), species.* from species, sighting where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName='" . $siteInfo["Name"]. "' order by species.objectid"));
  }
  else
  {
	  $gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species where sighting.LocationName='" . $siteInfo["Name"] . "' and sighting.SpeciesAbbreviation=species.Abbreviation group by sighting.SpeciesAbbreviation order by speciesid";
?>
	  <div class=heading>Observed <?= $speciesCount ?> species at this location on <?= $tripCount ?> trips</div>
<?
	  $annualLocationTotal = performQuery("select count(distinct sighting.SpeciesAbbreviation) as count, year(sighting.TripDate) as year from sighting, location where sighting.LocationName='" . $siteInfo["Name"] . "' group by year");

	  formatSpeciesByYearTable($gridQueryString, "&locationid=" . $siteInfo["objectid"], $annualLocationTotal);
  }
?>

</div>
</body>
</html>
