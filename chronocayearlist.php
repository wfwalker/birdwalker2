
<?php

require("./birdwalker.php");

$theYear = param($_GET, "year", "1996");

performQuery("CREATE TEMPORARY TABLE tmp (
      SpeciesAbbreviation varchar(16) default NULL,
      TripDate date default NULL,
      objectid varchar(16) default NULL);");

// here's what section 3.6.4 of the mysql manual calls:
// "a quite inefficient trick called the MAX-CONCAT trick"
// TODO upgrade to mysql 4.1 and use a subquery
performQuery("
      INSERT INTO tmp
        SELECT SpeciesAbbreviation,
          LEFT(        MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ), 10) AS TripDate,
          0+SUBSTRING( MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ),  11) AS objectid
        FROM sighting, location
        WHERE location.State='CA' and sighting.LocationName=location.Name AND Year(sighting.TripDate)='" . $theYear . "'
        GROUP BY SpeciesAbbreviation");

// TODO count rows in the first sightings table!

$firstSightingQuery = performQuery("SELECT
     date_format(sighting.TripDate, '%M %e, %Y') as niceDate,
     sighting.*, 
     species.CommonName,
     species.objectid as speciesid,
     trip.objectid as tripid, location.County, location.State
  FROM sighting, tmp, species, location, trip
  WHERE species.ABACountable='1' AND
     sighting.SpeciesAbbreviation=tmp.SpeciesAbbreviation AND
     species.Abbreviation=sighting.SpeciesAbbreviation AND
     sighting.TripDate=tmp.TripDate AND
     location.Name=sighting.LocationName AND
     location.State='CA' and
     trip.Date=sighting.TripDate
  ORDER BY TripDate DESC, LocationName, species.objectid;");

$speciesCount = mysql_num_rows($firstSightingQuery);
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Chronological ABA California <?= $theYear ?> List</title>
  </head>
  <body>

<?php
globalMenu();
browseButtons("./chronocayearlist.php?year=", $theYear, 1996, $theYear - 1, $theYear + 1, 2004);
navTrailBirds();
?>

<div class=contentright>
  <div class="titleblock">	  
    <div class=pagetitle>ABA California <?= $theYear ?> List</div>
      <div class=pagesubtitle><? echo $speciesCount ?> Species</div>
    </div>

<p class=sighting-notes>
Note: within a single day, the order of sightings is not
preserved.
</p>

<table class=report-content columns=4 width="600px">

<?
while($sightingInfo = mysql_fetch_array($firstSightingQuery))
{ ?>
    <tr>
      <td nowrap>

<?	if ($prevSightingInfo['TripDate'] != $sightingInfo['TripDate']) { ?>
		<a href="./tripdetail.php?id=<?= $sightingInfo['tripid'] ?>"><?= $sightingInfo['niceDate'] ?></a>
<?	} ?>

	</td>

  <td align=right>&nbsp;</td>

	<td><a href="./speciesdetail.php?id=<?= $sightingInfo['speciesid'] ?>"><?= $sightingInfo['CommonName'] ?></a>

<?  if ($sightingInfo["Photo"] == "1") { ?>
        <?= getPhotoLinkForSightingInfo($sightingInfo) ?>
<?  }
    editLink("./sightingedit.php?id=" . $sightingInfo['objectid']); ?>

	</td>
	</tr>
	
<?	if ($sightingInfo["Notes"] != "") { ?>
        <tr><td></td><td></td><td colspan=2 class=sighting-notes><?= $sightingInfo["Notes"] ?></td></tr>
<?  }

	$prevSightingInfo = $sightingInfo;
}

performQuery("DROP TABLE tmp;");

?>

</table>

    </div>
  </body>
</html>