
<?php

require("/Users/walker/Sites/birdwalker/birdwalker.php");

$speciesInfo = getSpeciesInfo($_GET['id']);
$orderInfo = getOrderInfo($_GET['id']);
$familyInfo = getFamilyInfo($_GET['id']);

$tripWhereClause = "'" . $speciesInfo["Abbreviation"] . "'=sighting.SpeciesAbbreviation and sighting.TripDate=trip.Date";
$speciesTripListQuery = getTripQuery($tripWhereClause);
$speciesTripCount = getTripCount($tripWhereClause);

$locationWhereClause = " '" . $speciesInfo["Abbreviation"] . "'=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name";
$speciesLocationListQuery = getLocationQuery($locationWhereClause);
$speciesLocationCount = getLocationCount($locationWhereClause);

?>

<html>

<head>
  <link title="Style" href="/~walker/birdwalker/stylesheet.css" type="text/css" rel="stylesheet">
  <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

  <div class=contentright>
	<div class="titleblock">
      <div class="pagetitle"><?php echo $speciesInfo["CommonName"] ?></div>
      <div class="pagesubtitle"><?php echo $speciesInfo["LatinName"] ?></div>
      <div class="metadata">
	    Family:
        <a href="./familydetail.php?family=<?php echo $familyInfo["objectid"] / pow(10, 7) ?>">
          <?php echo $familyInfo["LatinName"] ?>, <?php echo $familyInfo["CommonName"] ?>
        </a>
      </div>
      <div class="metadata">
	    Order:
	    <a href="./orderindex.php?order=<?php echo $orderInfo["objectid"] / pow(10, 9) ?>">
	      <?php echo $orderInfo["LatinName"] ?>, <?php echo $orderInfo["CommonName"] ?>
        </a>
<?php if (strlen($speciesInfo["ReferenceURL"]) > 0) {
      echo "<div><a href=\"" . $speciesInfo["ReferenceURL"] . "\">See also...</a></div>";
} ?>
      </div>
	</div>

    <div class=sighting-notes><?php echo $speciesInfo["Notes"] ?></div>


<?php
  if ($speciesTripCount < 5)
  {
	  echo "<div class=\"titleblock\">Seen on " . $speciesTripCount . " trips</div>";

	  // list the trips that included this species
	  while($tripInfo = mysql_fetch_array($speciesTripListQuery)) {
		  echo "<div class=firstcell><a href=/~walker/birdwalker/tripdetail.php?id=" . $tripInfo["objectid"] . ">" . $tripInfo["Name"] . " (" . $tripInfo["niceDate"] .  ")</a></div>";
	  }

	  echo "<div class=\"titleblock\">Seen in ". $speciesLocationCount . " locations</div>";

	  formatLocationList($speciesLocationCount, $speciesLocationListQuery);

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
