
<?php

require("./birdwalker.php");

$speciesID = param($_GET, 'id', 22330150100);
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
speciesBrowseButtons($speciesID, "byyear");
navTrailSpecies($speciesID);
?>

  <div class=contentright>
    <div class="pagesubtitle"><?= $speciesInfo["LatinName"] ?></div>
	<div class="titleblock">
<?    rightThumbnailSpecies($speciesInfo["Abbreviation"]); ?>
      <div class="pagetitle"><?= $speciesInfo["CommonName"] ?></div>
      <div class=metadata>

<? $sightingDates = performOneRowQuery("SELECT
        date_format(min(TripDate), '%M %e, %Y') AS earliest,
        date_format(max(TripDate), '%M %e, %Y') AS latest
      FROM sighting
      WHERE sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'");


    if (strlen($speciesInfo["ReferenceURL"]) > 0) { ?>
        <div><a href="<?= $speciesInfo["ReferenceURL"] ?>">See also...</a></div>
<?  }
    if ($speciesInfo["ABACountable"] == '0') { ?>
        <div>NOT ABA COUNTABLE</div>
<?  }
    speciesViewLinks($speciesID); ?>

     </div>
   </div>


   <div class=sighting-notes><?= $speciesInfo["Notes"] ?></div>

   <div class=heading><?= $speciesTripCount ?> trips, <?= $speciesLocationCount ?> locations</div>

<? formatLocationByYearTable(
     "WHERE sighting.LocationName=location.Name AND sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'",
     "./sightinglist.php?speciesid=" . $speciesID . "&"); ?>

</body>
</html>
