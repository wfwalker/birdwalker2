
<?php

require("./birdwalker.php");

$locationID = $_GET['id'];
$siteInfo = getLocationInfo($locationID);

$speciesCount = performCount("
    SELECT COUNT(DISTINCT species.objectid)
      FROM species, sighting
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation
      AND sighting.LocationName='" . $siteInfo["Name"]. "'");

$tripQuery = performQuery("
    SELECT DISTINCT trip.objectid, trip.*, date_format(Date, '%M %e, %Y') AS niceDate,
      COUNT(DISTINCT sighting.SpeciesAbbreviation) AS tripCount
      FROM trip, sighting
      WHERE sighting.LocationName='" . $siteInfo["Name"]. "'
      AND sighting.TripDate=trip.Date
      GROUP BY trip.Date
      ORDER BY trip.Date DESC");

$tripCount = mysql_num_rows($tripQuery);

$firstLocationID = performCount("
    SELECT objectid FROM location ORDER BY CONCAT(State,County,Name) LIMIT 1");
$lastLocationID = performCount("
    SELECT objectid FROM location ORDER BY CONCAT(State,County,Name) DESC LIMIT 1");

$nextLocationID = performCount("
    SELECT objectid FROM location
      WHERE CONCAT(State,County,Name) > '" . $siteInfo["State"] . $siteInfo["County"] . $siteInfo["Name"] . "'
      ORDER BY CONCAT(State,County,Name) LIMIT 1");
$prevLocationID = performCount("
    SELECT objectid FROM location
      WHERE CONCAT(State,County,Name) < '" . $siteInfo["State"] . $siteInfo["County"] . $siteInfo["Name"] . "'
      ORDER BY CONCAT(State,County,Name) DESC LIMIT 1");

$locationSightings = performQuery("
    SELECT sighting.objectid FROM sighting, location
      WHERE sighting.LocationName=location.Name
      AND location.objectid='" . $locationID ."'");

$firstSightings = getFirstSightings();
$locationFirstSightings = 0;

while($sightingInfo = mysql_fetch_array($locationSightings)) {
	if ($firstSightings[$sightingInfo['objectid']] != null) {
		$locationFirstSightings++;
	}
}

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $siteInfo["Name"] ?></title>
</head>

<body>

<?php
globalMenu();
browseButtons("./locationdetail.php?id=", $locationID, $firstLocationID, $prevLocationID, $nextLocationID, $lastLocationID);

$items[] = "
    <a href=\"./statelocations.php?state=" .  $siteInfo["State"] . "\">" .
      strtolower(getStateNameForAbbreviation($siteInfo["State"])) . "
    </a>";
$items[] = "
    <a href=\"./countylocations.php?county=" . $siteInfo["County"] . "&state=" . $siteInfo["State"] . "\">" .
      strtolower($siteInfo["County"]) . " county
    </a>";
$items[] =
    strtolower($siteInfo["Name"]);

navTrailLocations($items);
pageThumbnail("
    SELECT *, rand() AS shuffle
      FROM sighting
      WHERE Photo='1' AND LocationName='" . $siteInfo["Name"] . "'
      ORDER BY shuffle");
?>

<div class="contentright">
  <div class="titleblock">
    <div class=pagetitle><?= $siteInfo["Name"] ?></div>

<? if (strlen($siteInfo["ReferenceURL"]) > 0) { ?>
	<div><a href="<?= $siteInfo["ReferenceURL"] ?>">See also...</a></div>
<? }

   if (getEnableEdit()) { ?>
	<div><a href="./locationcreate.php?id=<?= $locationID ?>">edit</a></div>
<? }
   if (strlen($siteInfo["Latitude"]) > 0) { ?>
	<div>
      <a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude=<?= $siteInfo["Latitude"] ?>&longitude=-<?= $siteInfo["Longitude"] ?>">Map...</a>
    </div>
<? } ?>

    </div>

    <p class=sighting-notes><?= $siteInfo["Notes"] ?></p>

<? if ($tripCount < 5) { ?>

     <div class="heading"><?= $tripCount ?> trip<? if ($tripCount > 1) echo 's' ?></div>

<? while($tripInfo = mysql_fetch_array($tripQuery)) { ?>

       <div class=firstcell>
           <a href="./tripdetail.php?id=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["Name"] ?> (<?= $tripInfo["niceDate"] ?>)</a>
       </div>

<? } ?>

   <div class=heading>
	 <?= $speciesCount ?> species<? if ($locationFirstSightings > 0) {  echo ','; ?>
     <?= $locationFirstSightings ?> first sighting<? if ($locationFirstSightings > 1) echo 's' ?>
<?  }?>
   </div>

<? formatTwoColumnSpeciesList(performQuery("
        SELECT distinct(species.objectid), species.*
          FROM species, sighting
          WHERE species.Abbreviation=sighting.SpeciesAbbreviation
          AND sighting.LocationName='" . $siteInfo["Name"]. "'
          ORDER BY species.objectid"));
  }
  else
  { ?>
	  <div class=heading>
          <?= $speciesCount ?> species,
          <?= $tripCount ?> trips<? if ($locationFirstSightings > 0) {  echo ','; ?>
          <?= $locationFirstSightings ?> first sighting<? if ($locationFirstSightings > 1) echo 's'; } ?>
      </div>

<?   $gridQueryString="
        SELECT DISTINCT(CommonName), species.objectid AS speciesid, bit_or(1 << (year(TripDate) - 1995)) AS mask
          FROM sighting, species
          WHERE sighting.LocationName='" . $siteInfo["Name"] . "' AND sighting.SpeciesAbbreviation=species.Abbreviation
          GROUP BY sighting.SpeciesAbbreviation
          ORDER BY speciesid";

	  $annualLocationTotal = performQuery("
        SELECT COUNT(DISTINCT sighting.SpeciesAbbreviation) AS count, year(sighting.TripDate) AS year
          FROM sighting, location
          WHERE sighting.LocationName='" . $siteInfo["Name"] . "'
          GROUP BY year");

	  formatSpeciesByYearTable($gridQueryString, "&locationid=" . $siteInfo["objectid"], $annualLocationTotal);
  } ?>

</div>
</body>
</html>
