
<?php

require("./birdwalker.php");

$speciesInfo = getSpeciesInfo($_GET['id']);
$orderInfo = getOrderInfo($_GET['id']);
$familyInfo = getFamilyInfo($_GET['id']);

$tripWhereClause = "'" . $speciesInfo["Abbreviation"] . "'=sighting.SpeciesAbbreviation and sighting.TripDate=trip.Date";
$speciesTripQuery = performQuery( "select sighting.Notes as sightingNotes, trip.*, date_format(Date, '%M %e, %Y') as niceDate from trip, sighting where " . $tripWhereClause . " order by trip.Date desc");
$speciesTripCount = getTripCount($tripWhereClause);

$locationWhereClause = " '" . $speciesInfo["Abbreviation"] . "'=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name";
$speciesLocationListQuery = performQuery( "select distinct(location.objectid), location.* from location, sighting where " . $locationWhereClause . " order by State, County, Name");
$speciesLocationCount = performCount("select count(distinct location.objectid) from location, sighting where " . $locationWhereClause);

?>

<html>

<head>
  <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
  <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

  <div class=contentright>
	<div class="titleblock">
      <div class="pagetitle"><?php echo $speciesInfo["CommonName"] ?></div>
      <div class="pagesubtitle"><?php echo $speciesInfo["LatinName"] ?></div>
      <div class="metadata">
	    <a href="./familydetail.php?family=<?php echo $familyInfo["objectid"] / pow(10, 7) ?>">
          Family <?php echo $familyInfo["LatinName"] ?>, <?php echo $familyInfo["CommonName"] ?>
        </a>
      </div>
      <div class="metadata">
	    <a href="./orderdetail.php?order=<?php echo $orderInfo["objectid"] / pow(10, 9) ?>">
	      Order <?php echo $orderInfo["LatinName"] ?>, <?php echo $orderInfo["CommonName"] ?>
        </a>
<?php if (strlen($speciesInfo["ReferenceURL"]) > 0) {
      echo "<div><a href=\"" . $speciesInfo["ReferenceURL"] . "\">See also...</a></div>";
} ?>
      </div>
	</div>

    <div class=sighting-notes><?php echo $speciesInfo["Notes"] ?></div>

    <div class=titleblock>Seen on <?php echo $speciesTripCount ?> trips at <?php echo $speciesLocationCount ?> locations</div>

<?php
  if ($speciesTripCount < 5)
  {
	  // list the trips that included this species
	  while($tripInfo = mysql_fetch_array($speciesTripQuery)) {
		  echo "<div class=firstcell><a href=\"./tripdetail.php?id=" . $tripInfo["objectid"] . "\">" . $tripInfo["Name"] . " (" . $tripInfo["niceDate"] .  ")</a></div>";
		  echo "<div class=sighting-notes>" . $tripInfo["sightingNotes"] . "</div>";
	  }

	  echo "<div class=\"titleblock\">Seen in ". $speciesLocationCount . " locations</div>";

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

	  formatLocationByYearTable($speciesLocationCount, $gridQueryString);
   }

?>

</body>
</html>
