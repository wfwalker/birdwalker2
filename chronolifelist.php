
<?php

require("./birdwalker.php");

performQuery("CREATE TEMPORARY TABLE tmp ( abbrev varchar(16) default NULL, tripdate date default NULL);");
performQuery("INSERT INTO tmp SELECT SpeciesAbbreviation, MIN(TripDate) FROM sighting where Exclude!='1' GROUP BY SpeciesAbbreviation;");
$firstSightingQuery = performQuery("SELECT date_format(sighting.TripDate, '%M %e, %Y') as niceDate, sighting.*, species.CommonName, species.objectid as speciesid, trip.objectid as tripid, location.County, location.State FROM sighting, tmp, species, location, trip WHERE species.ABACountable='1' and sighting.SpeciesAbbreviation=tmp.abbrev AND species.Abbreviation=sighting.SpeciesAbbreviation AND sighting.TripDate=tmp.tripdate AND location.Name=sighting.LocationName AND trip.Date=sighting.TripDate order by TripDate, LocationName;");

$speciesCount = mysql_num_rows($firstSightingQuery);
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Chronological ABA Life List</title>
  </head>
  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailBirds(); ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>ABA Life List</div>
        <div class=pagesubtitle><? echo $speciesCount ?> Species</div>
      </div>

<p class=sighting-notes>
Note: within a single day, the order of sightings is not
preserved.
</p>

<table class=report-content columns=4 width="600px">
<?php
$counter = 1;
while($sightingInfo = mysql_fetch_array($firstSightingQuery)) {
	if (100 * floor($counter / 100) == $counter) { echo "<tr class=titleblock>"; } else { echo "<tr>"; }
	// date
	echo "<td nowrap>";
	if ($prevSightingInfo['TripDate'] != $sightingInfo['TripDate']) {
		echo "<a href=\"./tripdetail.php?id=" . $sightingInfo['tripid'] . "\">" . $sightingInfo['niceDate'] . "</a>";
	}
	echo "</td>";

	// count
	echo "<td align=right>" . $counter . "</td>";

	// species
	echo "<td><a href=\"./speciesdetail.php?id=" . $sightingInfo['speciesid'] . "\">" . $sightingInfo['CommonName'] . "</a>";
	// edit link
	if (getEnableEdit()) { echo " <a href=\"./sightingedit.php?id=" . $sightingInfo['objectid'] . "\">edit</a>"; }
	echo" </td>";
	echo "</tr>";
	
	// notes
	if ($sightingInfo["Notes"] != "") { echo "<tr><td></td><td></td><td colspan=2 class=sighting-notes>" . $sightingInfo["Notes"] . "</td></tr>"; }

	$counter++;
	$prevSightingInfo = $sightingInfo;
}


performQuery("DROP TABLE tmp;");

?>
    </div>
  </body>
</html>