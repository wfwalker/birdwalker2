
<?php

require("./birdwalker.php");

performQuery("
    CREATE TEMPORARY TABLE tmp ( abbrev varchar(16) default NULL, tripdate date default NULL);");

performQuery("
    INSERT INTO tmp
      SELECT SpeciesAbbreviation, MIN(TripDate)
      FROM sighting, species
      WHERE Exclude!='1' and species.Abbreviation=sighting.SpeciesAbbreviation
      GROUP BY SpeciesAbbreviation
      ORDER BY species.objectid;");

$firstSightingQuery = performQuery("SELECT
     date_format(sighting.TripDate, '%M %e, %Y') as niceDate,
     sighting.*, 
     species.CommonName,
     species.objectid as speciesid,
     trip.objectid as tripid, location.County, location.State
  FROM sighting, tmp, species, location, trip
  WHERE species.ABACountable='1' AND
     sighting.SpeciesAbbreviation=tmp.abbrev AND
     species.Abbreviation=sighting.SpeciesAbbreviation AND
     sighting.TripDate=tmp.tripdate AND
     location.Name=sighting.LocationName AND
     trip.Date=sighting.TripDate
  ORDER BY TripDate, LocationName;");

$speciesCount = mysql_num_rows($firstSightingQuery);
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Chronological ABA Life List</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

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
while($sightingInfo = mysql_fetch_array($firstSightingQuery))
{
	if (100 * floor($counter / 100) == $counter)
	{ ?>
		<tr class=titleblock>
<?	} else { ?>
		<tr>
<?	} ?>
	<td nowrap>

<?	if ($prevSightingInfo['TripDate'] != $sightingInfo['TripDate'])
	{ ?>
		<a href="./tripdetail.php?id=<?= $sightingInfo['tripid'] ?>"><?= $sightingInfo['niceDate'] ?></a>
<?	} ?>

	</td>

	<td align=right><?= $counter ?></td>
    <td><a href="./speciesdetail.php?id=<?= $sightingInfo['speciesid'] ?>"><?= $sightingInfo['CommonName'] ?></a>

<?  if ($sightingInfo["Photo"] == "1") { ?>
        <?= getPhotoLinkForSightingInfo($sightingInfo) ?>
<?
    }

    editLink("./sightingedit.php?id=" . $sightingInfo['objectid']); ?>

	</td>
	</tr>
	
<?	if ($sightingInfo["Notes"] != "")
	{ ?>
		<tr><td></td><td></td><td colspan=2 class=sighting-notes><?= $sightingInfo["Notes"] ?></td></tr>
<?	}

	$counter++;
	$prevSightingInfo = $sightingInfo;
}


performQuery("DROP TABLE tmp;");

?>

</table>

    </div>
  </body>
</html>