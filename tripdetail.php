<html>

<?php

require("./birdwalker.php");

$tripID = $_GET['id'];
$tripInfo = getTripInfo($tripID);
$tripYear = substr($tripInfo["Date"], 0, 4);
$tripCount = performCount("select max(objectid) from trip");

$locationListQuery = performQuery("select distinct(location.objectid), location.Name from location, sighting where location.Name=sighting.LocationName and sighting.TripDate='". $tripInfo["Date"] . "'");

$firstSightings = getFirstSightings();
$firstYearSightings = getFirstYearSightings(substr($tripInfo["Date"], 0, 4));

// how many first sightings were on this trip?
$tripSightings = performQuery("select objectid from sighting where TripDate='" . $tripInfo['Date'] . "'");

// total species count for this trip
$tripSpeciesCount = mysql_num_rows($tripSightings);

$tripFirstSightings = 0;
while($sightingInfo = mysql_fetch_array($tripSightings)) {
	if ($firstSightings[$sightingInfo['objectid']] != null) { $tripFirstSightings++; }
}


?>

  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $tripInfo["Name"] ?></title>
  </head>

  <body>

<?php
globalMenu();
browseButtons("./tripdetail.php?id=", $tripID, 1, $tripID - 1, $tripID + 1, $tripCount);
$items[] = "<a href=\"./tripindex.php#" . $tripYear . "\">" . $tripYear . "</a>";
$items[] = strtolower($tripInfo["Name"]);
navTrailTrips($items);
pageThumbnail("select *, rand() as shuffle from sighting where Photo='1' and TripDate='" . $tripInfo["Date"] . "' order by shuffle");
?>

    <div class="contentright">
	  <div class=titleblock>
      <div class=pagetitle> <?= $tripInfo["Name"] ?></div>
      <div class=pagesubtitle> <?= $tripInfo["niceDate"] ?></div>
      <div class=metadata>Led by  <?= $tripInfo["Leader"] ?></div>
      <div class=metadata>Observed  <?= $tripSpeciesCount ?> species on this trip<? if ($tripFirstSightings > 0) echo ", including " . $tripFirstSightings . " first sightings" ?></div>
<?php
if (strlen($tripInfo["ReferenceURL"]) > 0) {
    echo "<div><a href=\"" . $tripInfo["ReferenceURL"] . "\">See also...</a></div>";
}
if (getEnableEdit()) {
	echo "<div><a href=\"./tripedit.php?id=" . $tripID . "\">edit</a></div>";
}
?>
    </div>

      <div class=sighting-notes> <?= $tripInfo["Notes"] ?></div>

<?php

while($locationInfo = mysql_fetch_array($locationListQuery))
{
	$tripLocationQuery = performQuery("select species.CommonName, species.ABACountable, species.objectid as speciesid, sighting.* from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.TripDate='". $tripInfo["Date"] . "' and sighting.LocationName='" . $locationInfo["Name"] . "' order by species.objectid");
	$tripLocationCount = mysql_num_rows($tripLocationQuery);
	$divideByTaxo = ($tripLocationCount > 30);
?>
    <div class="heading">
        <a href="./locationdetail.php?id=<?= $locationInfo["objectid"]?>"><?= $locationInfo["Name"] ?></a>, <?= $tripLocationCount ?> species
    </div>
	<div style="padding-left: 20px">
<?
	while($info = mysql_fetch_array($tripLocationQuery))
	{
		$orderNum =  floor($info["objectid"] / pow(10, 9));
		
		if ($divideByTaxo && (getBestTaxonomyID($prevInfo["speciesid"]) != getBestTaxonomyID($info["speciesid"])))
		{
			$taxoInfo = getBestTaxonomyInfo($info["speciesid"]);
?>
            <div class="heading"><?= $taxoInfo["CommonName"] ?></div>
<?
		}
?>
		<div class=firstcell><a href="./speciesdetail.php?id=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a>

		<span class=noteworthy-species>
<?
		if ($info["Exclude"] == "1") { ?> excluded <? }
		if ($info["Photo"] == "1") { echo getPhotoLinkForSightingInfo($info); }

		$sightingID = $info["objectid"];

		if (getEnableEdit()) { ?><a href="./sightingedit.php?id=<?= $sightingID ?>">edit</a><? }

		if ($firstSightings[$sightingID] != null) { ?> first life sighting <? }
		else if ($firstYearSightings[$sightingID] != null) { ?> first <?= $tripYear ?> sighting <? }

		if ($info["ABACountable"] == '0') { ?> NOT ABA COUNTABLE <? }
?>
        </div>
<?
		if (strlen($info["Notes"]) > 0) { ?><div class=sighting-notes><?= $info["Notes"] ?></div><? }
 
		$prevInfo = $info;
	}
?>
	</div>
<?
}
?>

    </div>
  </body>
</html>
