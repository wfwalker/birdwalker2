
<?php

require("./birdwalker.php");

$speciesID = $_GET['id'];
$speciesInfo = getSpeciesInfo($speciesID);

$speciesTripQuery = performQuery("
    SELECT sighting.Notes as sightingNotes, trip.*, date_format(Date, '%M %e, %Y') as niceDate
      FROM trip, sighting
      WHERE '" . $speciesInfo["Abbreviation"] . "'=sighting.SpeciesAbbreviation
      AND sighting.TripDate=trip.Date
      ORDER BY trip.Date desc");
$speciesTripCount = mysql_num_rows($speciesTripQuery);

$speciesLocationListQuery = performQuery( "
    SELECT distinct(location.objectid), location.*
      FROM location, sighting
      WHERE sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'
      AND sighting.LocationName=location.Name
      ORDER BY State, County, Name");

$speciesLocationCount = mysql_num_rows($speciesLocationListQuery);

?>

<html>

<head>
  <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet"/>
  <title>birdWalker | <?= $speciesInfo["CommonName"] ?></title>
</head>

<body>

<?php
globalMenu();
speciesBrowseButtons($speciesID, "bymonth");
navTrailSpecies($speciesID);
?>

  <div class=contentright>
    <div class="pagesubtitle"><?= $speciesInfo["LatinName"] ?></div>
	<div class="titleblock">
<?  rightThumbnailSpecies($speciesInfo["Abbreviation"]); ?>
      <div class="pagetitle"><?= $speciesInfo["CommonName"] ?></div>
      <div class=metadata>

<?  if (strlen($speciesInfo["ReferenceURL"]) > 0) { ?>
        <div><a href="<?= $speciesInfo["ReferenceURL"] ?>">See also...</a></div>
<?  }
    if ($speciesInfo["ABACountable"] == '0') { ?>
        <div>NOT ABA COUNTABLE</div>
<?  }
    speciesViewLinks($speciesID); ?>

   </div>
   </div>

   <div class=sighting-notes><?= $speciesInfo["Notes"] ?></div>

    <div class=heading>
		<?= $speciesTripCount ?> trips, <?= $speciesLocationCount ?> locations
    </div>

<?  $gridQueryString="
              SELECT distinct(LocationName), County, State,
                location.objectid as locationid, bit_or(1 << month(TripDate)) as mask
                FROM sighting, location
                WHERE sighting.LocationName=location.Name
                AND sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'
                GROUP BY sighting.LocationName
                ORDER BY location.State, location.County, location.Name;";

    formatLocationByMonthTable($gridQueryString, "./sightinglist.php?speciesid=" . $speciesID . "&"); ?>

</body>
</html>
