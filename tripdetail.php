<?php

require("./birdwalker.php");

$tripInfo = getTripInfo($_GET['id']);

$whereClause = "sighting.LocationName=location.Name and species.Abbreviation=sighting.SpeciesAbbreviation and sighting.TripDate='" . $tripInfo["Date"]. "'";

// total species countv for this trip
$tripSpeciesCount = getFancySpeciesCount($whereClause);

// get list of species, ordered first by location name then taxo order; retrieve location name as additional field
$tripSpeciesQuery = getFancySpeciesQuery($whereClause, "sighting.LocationName, species.objectID", ", sighting.LocationName, location.objectid as locationid, sighting.Notes, sighting.Exclude, sighting.objectid as sightingid");

$firstSightings = getFirstSightings();
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $tripInfo["Name"] ?></title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class="contentright">
	  <div class=titleblock>
      <div class=pagetitle> <?php echo $tripInfo["Name"] ?> Trip List</div>
      <div class=pagesubtitle> <?php echo $tripInfo["niceDate"] ?></div>
      <div class=metadata>Leader:  <?php echo $tripInfo["Leader"] ?></div>
<?php if (strlen($tripInfo["ReferenceURL"]) > 0) {
      echo "<div><a href=\"" . $tripInfo["ReferenceURL"] . "\">Trip Website</a></div>";
} ?>
	  </div>

      <div class=sighting-notes> <?php echo $tripInfo["Notes"] ?></div>

      <div class=titleblock>Observed  <?php echo $tripSpeciesCount ?> species on this trip</div>

<?php

// Show what we got
while($speciesInfo = mysql_fetch_array($tripSpeciesQuery)) {
  if (strcmp($prevInfo["LocationName"], $speciesInfo["LocationName"]))
  {
    echo "<div class=\"titleblock\"><a href=\"./locationdetail.php?id=" . $speciesInfo["locationid"] . "\">" . $speciesInfo["LocationName"] . "</a></div>";
  }

  echo "<div class=firstcell><a href=\"./speciesdetail.php?id=" . $speciesInfo["objectid"] . "\">" . $speciesInfo["CommonName"] . "</a>";

  if (strlen($speciesInfo["Notes"]) > 0) {
    echo "<div class=sighting-notes>" . $speciesInfo["Notes"] . "</div>";
  }

  if ($speciesInfo["Exclude"] == "1") {
    echo "<div class=sighting-notes>excluded</div>";
  }

  $sightingID = $speciesInfo["sightingid"];
  if ($firstSightings[$sightingID] != null)
  {
    echo "<div class=sighting-notes>first</div>";
  }

  echo "</div>";

  $prevInfo = $speciesInfo;
}

?>

    </div>
  </body>
</html>
