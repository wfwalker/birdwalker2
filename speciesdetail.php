
<?php

require("./birdwalker.php");

$speciesID = $_GET['id'];
$speciesInfo = getSpeciesInfo($speciesID);
$orderInfo = getOrderInfo($speciesID);
$familyInfo = getFamilyInfo($speciesID);

$speciesTripQuery = performQuery("
    SELECT sighting.Notes as sightingNotes, trip.*, date_format(Date, '%M %e, %Y') as niceDate
      FROM trip, sighting
      WHERE '" . $speciesInfo["Abbreviation"] . "'=sighting.SpeciesAbbreviation
      AND sighting.TripDate=trip.Date
      ORDER BY trip.Date desc");
$speciesTripCount = mysql_num_rows($speciesTripQuery);


$firstAndLastSpecies = performOneRowQuery("
    SELECT min(species.objectid) as firstOne, max(species.objectid) as lastOne
      FROM sighting, species
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation");

$firstSpecies = $firstAndLastSpecies["firstOne"];
$lastSpecies = $firstAndLastSpecies["lastOne"];

$nextSpecies = performCount("
    SELECT min(species.objectid)
      FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid>" . $speciesID . " LIMIT 1");

$prevSpecies = performCount("
    SELECT max(species.objectid)
      FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid<" . $speciesID . " LIMIT 1");

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
browseButtons("./speciesdetail.php?id=", $speciesID, $firstSpecies, $prevSpecies, $nextSpecies, $lastSpecies);
$items[] = "<a href=\"./orderdetail.php?order=" . $orderInfo["objectid"] / pow(10, 9) . "\">" . strtolower($orderInfo["LatinName"]) . "</a>";
$items[] = "<a href=\"./familydetail.php?family=" . $familyInfo["objectid"] / pow(10, 7) . "\">" . strtolower($familyInfo["LatinName"]) . "</a>";
$items[] = strtolower($speciesInfo["CommonName"]);
navTrailBirds($items);
pageThumbnail("
    SELECT * FROM sighting
      WHERE SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "' and Photo='1' ORDER BY TripDate DESC LIMIT 1");
?>

  <div class=contentright>
	<div class="titleblock">
      <div class="pagetitle"><?= $speciesInfo["CommonName"] ?></div>
      <div class="pagesubtitle"><?= $speciesInfo["LatinName"] ?></div>
      <div class=metadata>
<?php
if ($speciesTripCount >= 5)
{
    $sightingDates = performOneRowQuery("SELECT
        date_format(min(TripDate), '%M %e, %Y') AS earliest,
        date_format(max(TripDate), '%M %e, %Y') AS latest
      FROM sighting
      WHERE sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'"); ?>

    <div class=metadata><?= $sightingDates["earliest"] ?> - <?= $sightingDates["latest"] ?></div>
    <div class=metadata><?= $speciesTripCount ?> trips, <?= $speciesLocationCount ?> locations</div>

<?
}
    if (strlen($speciesInfo["ReferenceURL"]) > 0) { ?>
        <div><a href="<?= $speciesInfo["ReferenceURL"] ?>">See also...</a></div>
<?  }
    if ($speciesInfo["ABACountable"] == '0') { ?>
        <div>NOT ABA COUNTABLE</div>
<? } ?>

   </div>
   </div>

   <div class=sighting-notes><?= $speciesInfo["Notes"] ?></div>

<? if ($speciesTripCount < 5)
   { ?>
       <div class=heading><?= $speciesTripCount ?> trip<? if ($speciesTripCount > 1) echo 's' ?></div>

<?      // list the trips that included this species
		while($tripInfo = mysql_fetch_array($speciesTripQuery))
		{ ?>
			  <div class=firstcell>
                  <a href="./tripdetail.php?id=<?= $tripInfo["objectid"] ?>">
                      <?= $tripInfo["Name"] ?> (<?= $tripInfo["niceDate"] ?>)
                  </a>
              </div>
			  <div class=sighting-notes><?= $tripInfo["sightingNotes"] ?></div>
<?		} ?>
        <div class=heading><?= $speciesLocationCount ?> location<? if ($speciesLocationCount > 1) echo 's' ?></div>

<?		$prevInfo=null;

		while($info = mysql_fetch_array($speciesLocationListQuery))
		{ ?>
		    <div class=firstcell><a href="./locationdetail.php?id=<?= $info["objectid"] ?>"><?= $info["Name"] ?></a></div>
<?		    $prevInfo = $info;   
		} ?>

		</div>
<? }
	else
	{
	    $gridQueryString="
              SELECT distinct(LocationName), County, State,
                location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask
                FROM sighting, location
                WHERE sighting.LocationName=location.Name
                AND sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'
                GROUP BY sighting.LocationName
                ORDER BY location.State, location.County, location.Name;";

		  formatLocationByYearTable($gridQueryString, "./sightinglist.php?speciesid=" . $speciesID . "&");
    } ?>

</body>
</html>
