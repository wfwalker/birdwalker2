<?php

require("./birdwalker.php");

$tripInfo = getTripInfo($_GET['id']);
$tripYear = substr($tripInfo["Date"], 0, 4);
$tripCount = getTripCount();
$whereClause = "sighting.LocationName=location.Name and species.Abbreviation=sighting.SpeciesAbbreviation and sighting.TripDate='" . $tripInfo["Date"]. "'";

// total species countv for this trip
$tripSpeciesCount = getFancySpeciesCount($whereClause);

$locationListQuery = performQuery("select distinct(location.objectid), location.Name from location, sighting where location.Name=sighting.LocationName and sighting.TripDate='". $tripInfo["Date"] . "'");

$firstSightings = getFirstSightings();
$firstYearSightings = getFirstYearSightings(substr($tripInfo["Date"], 0, 4));
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $tripInfo["Name"] ?></title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class="navigationleft">
	  <a href="./tripdetail.php?id=1">first</a>
	  <a href="./tripdetail.php?id=<?php echo $_GET['id'] - 1 ?>">prev</a>
      <a href="./tripdetail.php?id=<?php echo $_GET['id'] + 1 ?>">next</a>
      <a href="./tripdetail.php?id=<?php echo $tripCount ?>">last</a>
    </div>

    <div class="contentright">
	  <div class=titleblock>
      <div class=pagetitle> <?php echo $tripInfo["Name"] ?> Trip List</div>
      <div class=pagesubtitle> <?php echo $tripInfo["niceDate"] ?></div>
      <div class=metadata>Led by  <?php echo $tripInfo["Leader"] ?></div>
<?php if (strlen($tripInfo["ReferenceURL"]) > 0) {
      echo "<div><a href=\"" . $tripInfo["ReferenceURL"] . "\">Trip Website</a></div>";
} ?>
    </div>

      <div class=sighting-notes> <?php echo $tripInfo["Notes"] ?></div>

      <div class=titleblock>Observed  <?php echo $tripSpeciesCount ?> species on this trip</div>

<?php

while($locationInfo = mysql_fetch_array($locationListQuery))
{
	$tripLocationQuery = performQuery("select species.CommonName, species.objectid as speciesid, sighting.* from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.TripDate='". $tripInfo["Date"] . "' and sighting.LocationName='" . $locationInfo["Name"] . "' order by species.objectid");
	$tripLocationCount = performCount("select count(distinct SpeciesAbbreviation) from sighting where sighting.TripDate='". $tripInfo["Date"] . "' and sighting.LocationName='" . $locationInfo["Name"] . "'");
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

		echo "<div class=firstcell><a href=\"./speciesdetail.php?id=".$info["speciesid"]."\">".$info["CommonName"]."</a>";

		if ($info["Exclude"] == "1") {
			echo "<span class=sighting-notes> excluded</span>";
		}
 
		$sightingID = $info["objectid"];
 
		if ($firstSightings[$sightingID] != null) echo "<span class=sighting-notes> first life sighting</span>";
		else if ($firstYearSightings[$sightingID] != null) echo "<span class=sighting-notes> first " . $tripYear . " sighting</span>";
		
		echo "</div>";

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
