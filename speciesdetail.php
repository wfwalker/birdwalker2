
<?php

require("./birdwalker.php");

$speciesID = $_GET['id'];
$speciesInfo = getSpeciesInfo($speciesID);
$orderInfo = getOrderInfo($speciesID);
$familyInfo = getFamilyInfo($speciesID);

$tripWhereClause = "'" . $speciesInfo["Abbreviation"] . "'=sighting.SpeciesAbbreviation and sighting.TripDate=trip.Date";
$speciesTripQuery = performQuery( "select sighting.Notes as sightingNotes, trip.*, date_format(Date, '%M %e, %Y') as niceDate from trip, sighting where " . $tripWhereClause . " order by trip.Date desc");
$speciesTripCount = mysql_num_rows($speciesTripQuery);

$locationWhereClause = " '" . $speciesInfo["Abbreviation"] . "'=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name";
$speciesLocationListQuery = performQuery( "select distinct(location.objectid), location.* from location, sighting where " . $locationWhereClause . " order by State, County, Name");
$speciesLocationCount = mysql_num_rows($speciesLocationListQuery);

$photoQuery = performQuery("select * from sighting where SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "' and Photo='1' order by TripDate desc");

$firstAndLastSpecies = performOneRowQuery("select min(species.objectid) as firstOne, max(species.objectid) as lastOne from sighting, species where sighting.SpeciesAbbreviation=species.Abbreviation");
$firstSpecies = $firstAndLastSpecies["firstOne"];
$lastSpecies = $firstAndLastSpecies["lastOne"];
$nextSpecies = performCount("select min(species.objectid) from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation and species.objectid>" . $speciesID);
$prevSpecies = performCount("select max(species.objectid) from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation and species.objectid<" . $speciesID);

$sightingDates = performOneRowQuery("select min(TripDate) as earliest, max(TripDate) as latest from sighting where sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "'");
?>

<html>

<head>
  <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet"/>
  <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?></title>
</head>

<body>

<?php
globalMenu();
browseButtons("./speciesdetail.php?id=", $speciesID, $firstSpecies, $prevSpecies, $nextSpecies, $lastSpecies);
$items[] = "<a href=\"./orderdetail.php?order=" . $orderInfo["objectid"] / pow(10, 9) . "\">" . strtolower($orderInfo["LatinName"]) . "</a>";
$items[] = "<a href=\"./familydetail.php?family=" . $familyInfo["objectid"] / pow(10, 7) . "\">" . strtolower($familyInfo["LatinName"]) . "</a>";
$items[] = strtolower($speciesInfo["CommonName"]);
navTrailBirds($items);
?>

<div class=thumb><?php if ($photoInfo = mysql_fetch_array($photoQuery)){ echo getThumbForSightingInfo($photoInfo); } ?></div>

  <div class=contentright>
	<div class="titleblock">
      <div class="pagetitle"><?php echo $speciesInfo["CommonName"] ?></div>
      <div class="pagesubtitle"><?php echo $speciesInfo["LatinName"] ?></div>
      <div class=metadata>
<?php
if ($speciesTripCount >= 5)
{
	echo "<div class=metadata>observed on " . $speciesTripCount . " trips  in " . $speciesLocationCount . " locations</div>";
	echo "<div class=metadata>first seen " . $sightingDates["earliest"] . ", last seen " . $sightingDates["latest"] . "</div>";
}

if (strlen($speciesInfo["ReferenceURL"]) > 0) { echo "<div><a href=\"" . $speciesInfo["ReferenceURL"] . "\">See also...</a></div>"; }
if ($speciesInfo["ABACountable"] == '0') { echo "<div>NOT ABA COUNTABLE</div>"; }
?>
      </div>
    </div>

    <div class=sighting-notes><?php echo $speciesInfo["Notes"] ?></div>

<?php
	  if ($speciesTripCount < 5)
	  {
		  echo "<div class=heading>Observed on " . $speciesTripCount . " trips</div>";

		  // list the trips that included this species
		  while($tripInfo = mysql_fetch_array($speciesTripQuery))
		  {
			  echo "<div class=firstcell><a href=\"./tripdetail.php?id=" . $tripInfo["objectid"] . "\">" . $tripInfo["Name"] . " (" . $tripInfo["niceDate"] .  ")</a></div>";
			  echo "<div class=sighting-notes>" . $tripInfo["sightingNotes"] . "</div>";
		  }

		  echo "<div class=heading>Observed at " . $speciesLocationCount . " locations</div>";

		  $prevInfo=null;

		  while($info = mysql_fetch_array($speciesLocationListQuery))
		  {
			  echo "<div class=firstcell><a href=\"./locationdetail.php?id=".$info["objectid"]."\">".$info["Name"]."</a></div>";
			  $prevInfo = $info;   
		  }

		  echo "</div>";
	  }
	  else
	  {
		  $gridQueryString="select distinct(LocationName), County, State, location.objectid as locationid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, location where sighting.LocationName=location.Name and sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "' group by sighting.LocationName order by location.State, location.County, location.Name;";

		  formatLocationByYearTable($gridQueryString, "./sightinglist.php?speciesid=" . $speciesID . "&");
	  }
?>

</body>
</html>
