<html>

<?php

require("./speciesquery.php");

$tripID = param($_GET, 'id', 343);
$tripInfo = getTripInfo($tripID);
$tripYear = substr($tripInfo["Date"], 0, 4);

$locationListQuery = performQuery("SELECT distinct(location.objectid), location.Name
  FROM location, sighting
  WHERE location.Name=sighting.LocationName and sighting.TripDate='". $tripInfo["Date"] . "'");
$locationCount = mysql_num_rows($locationListQuery);

$firstSightings = getFirstSightings();
$firstYearSightings = getFirstYearSightings(substr($tripInfo["Date"], 0, 4));

// how many life birds were on this trip?
$tripSightings = performQuery("
    SELECT sighting.objectid FROM sighting, species
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND TripDate='" . $tripInfo['Date'] . "'");

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
tripBrowseButtons($tripID, "detail");
$items[] = "<a href=\"./tripindex.php#" . $tripYear . "\">" . $tripYear . "</a>";
//$items[] = strtolower($tripInfo["Name"]);
navTrailTrips($items);
?>


    <div class="contentright">
      <div class=pagesubtitle> <?= $tripInfo["niceDate"] ?></div>

	  <div class=titleblock>

<?rightThumbnail("SELECT *, rand() AS shuffle
    FROM sighting WHERE Photo='1' AND TripDate='" . $tripInfo["Date"] . "'
    ORDER BY shuffle LIMIT 1");?>
        <div class=pagetitle>
            <?= $tripInfo["Name"] ?>
            <?= editLink("./tripedit.php?id=" . $tripID); ?>
        </div>
        <div class=metadata>Led by  <?= $tripInfo["Leader"] ?></div>

<? if (strlen($tripInfo["ReferenceURL"]) > 0) { ?>
          <div><a href="<?= $tripInfo["ReferenceURL"] ?>">See also...</a></div>
<? } ?>

         <div class=report-content><p><?= $tripInfo["Notes"] ?></p></div>
      </div>


<? if ($locationCount > 1) { ?>
          <div class=heading>Grand total, <?= $tripSpeciesCount ?> species<? if ($tripFirstSightings > 0) { ?>,
			<?= $tripFirstSightings ?> life bird<? if ($tripFirstSightings > 1) echo 's'; } ?>
          </div>
<? }

while($locationInfo = mysql_fetch_array($locationListQuery))
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setTripID($tripID);
	$speciesQuery->setLocationID($locationInfo["objectid"]);

	$tripLocationQuery = performQuery("SELECT
        species.CommonName, species.ABACountable, species.objectid, sighting.Notes, sighting.Exclude,
        sighting.Photo, sighting.objectid AS sightingid
      FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation AND
        sighting.TripDate='". $tripInfo["Date"] . "' AND
        sighting.LocationName='" . $locationInfo["Name"] . "'
      ORDER BY species.objectid");

	$locationFirstSightings = 0;
	while($sightingInfo = mysql_fetch_array($tripLocationQuery)) {
		if ($firstSightings[$sightingInfo['sightingid']] != null) { $locationFirstSightings++; }
	}
	mysql_data_seek($tripLocationQuery, 0);

	$tripLocationCount = mysql_num_rows($tripLocationQuery); ?>

    <div class="heading">
        <a href="./locationdetail.php?id=<?= $locationInfo["objectid"]?>"><?= $locationInfo["Name"] ?></a>,
        <?= $tripLocationCount ?> species<? if ($locationFirstSightings > 0) { ?>,
        <?= $locationFirstSightings ?> life bird<? if ($locationFirstSightings > 1) echo 's'; } ?>
    </div>

    <? $speciesQuery->formatTwoColumnSpeciesList(); ?>
<? }?>
    </div>
  </body>
</html>
