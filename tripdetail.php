<?php

require("./birdwalker.php");

$tripID = $_GET['id'];
$tripInfo = getTripInfo($tripID);
$tripYear = substr($tripInfo["Date"], 0, 4);
$tripCount = getTripCount();
$whereClause = "sighting.LocationName=location.Name and species.Abbreviation=sighting.SpeciesAbbreviation and sighting.TripDate='" . $tripInfo["Date"]. "'";

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

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $tripInfo["Name"] ?></title>
  </head>

  <body>

<?php navigationHeader(); navigationButtons("./tripdetail.php?id=", $tripID, 1, $tripID - 1, $tripID + 1, $tripCount); ?>

    <div class="contentright">
	  <div class=titleblock>
      <div class=pagetitle> <?php echo $tripInfo["Name"] ?> Trip List</div>
      <div class=pagesubtitle> <?php echo $tripInfo["niceDate"] ?></div>
      <div class=metadata>Led by  <?php echo $tripInfo["Leader"] ?></div>
<?php
if (strlen($tripInfo["ReferenceURL"]) > 0) {
    echo "<div><a href=\"" . $tripInfo["ReferenceURL"] . "\">See also...</a></div>";
}
if (getEnableEdit()) {
	echo "<div><a href=\"./tripedit.php?id=" . $tripID . "\">edit</a></div>";
}
?>
    </div>

      <div class=sighting-notes> <?php echo $tripInfo["Notes"] ?></div>

	<div class=titleblock>Observed  <?php echo $tripSpeciesCount ?> species on this trip<? if ($tripFirstSightings > 0) echo ", including " . $tripFirstSightings . " first sightings" ?></div>

<?php

while($locationInfo = mysql_fetch_array($locationListQuery))
{
	$tripLocationQuery = performQuery("select species.CommonName, species.objectid as speciesid, sighting.* from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.TripDate='". $tripInfo["Date"] . "' and sighting.LocationName='" . $locationInfo["Name"] . "' order by species.objectid");
	$tripLocationCount = mysql_num_rows($tripLocationQuery);
	$divideByTaxo = ($tripLocationCount > 30);

	echo "<div class=\"titleblock\"><a href=\"./locationdetail.php?id=" . $locationInfo["objectid"] . "\">" . $locationInfo["Name"] . "</a>, " . $tripLocationCount . " species</div>";
	echo "<div style=\"padding-left: 20px\">";
	
	while($info = mysql_fetch_array($tripLocationQuery))
	{
		$orderNum =  floor($info["objectid"] / pow(10, 9));
		
		if ($divideByTaxo && (getBestTaxonomyID($prevInfo["speciesid"]) != getBestTaxonomyID($info["speciesid"])))
		{
			$taxoInfo = getBestTaxonomyInfo($info["speciesid"]);
			echo "<div class=\"titleblock\">" . $taxoInfo["CommonName"] . "</div>";
		}

		echo "\n<div class=firstcell><a href=\"./speciesdetail.php?id=".$info["speciesid"]."\">" .
			$info["CommonName"] .
			"</a>";

		echo "\n<span class=noteworthy-species>";
		if ($info["Exclude"] == "1") {
			echo " excluded";
		}

		if ($info["Photo"] == "1") {
			echo " <a href=\"./photodetail.php?id=" . $info["objectid"] . "\"><img align=center src=\"./images/camera.gif\"></a>";
		}

		$sightingID = $info["objectid"];

		if (getEnableEdit()) { echo "\n <a href=\"./sightingedit.php?id=" . $sightingID . "\">edit</a>"; }

		if ($firstSightings[$sightingID] != null) echo " first life sighting";
		else if ($firstYearSightings[$sightingID] != null) echo " first " . $tripYear . " sighting";
		
		echo "\n</div>";

		if (strlen($info["Notes"]) > 0) {
			echo "<div class=sighting-notes>" . $info["Notes"] . "</div>";
		}
 
		$prevInfo = $info;
	}

	echo "</div>";
	
}

?>

    </div>
  </body>
</html>
