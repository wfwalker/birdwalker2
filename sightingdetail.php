
<?php

require("./birdwalker.php");
$sightingID = $_GET['id'];
$sightingInfo = getSightingInfo($sightingID);
$speciesInfo = performOneRowQuery("select * from species where Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
$tripInfo = performOneRowQuery("select *, date_format(Date, '%W,  %M %e, %Y') as niceDate from trip where Date='" . $sightingInfo["TripDate"] . "'");
$tripYear =  substr($tripInfo["Date"], 0, 4);
$locationInfo = performOneRowQuery("select * from location where Name='" . $sightingInfo["LocationName"] . "'");
$sightingCount = performCount("select max(objectid) from sighting");
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $speciesInfo["CommonName"] ?>,  <?= $tripInfo["niceDate"] ?></title>
</head>

<body>

<?php
globalMenu();
browseButtons("./sightingdetail.php?id=", $sightingID, 1, $sightingID - 1, $sightingID + 1, $sightingCount);
navTrailBirds();
?>

<div class="contentright">
<div class="titleblock">
	  <div class=pagetitle> <?= $speciesInfo["CommonName"] ?></div>
      <div class=pagesubtitle><?= $tripInfo["niceDate"] ?></div>
      <div class=metadata><?= $locationInfo["County"] ?> County, <?= getStateNameForAbbreviation($locationInfo["State"]) ?></div>
</div>


<table class=report-content columns=2 width=100%>
  <tr><td class=heading colspan=2>Sighting</td></tr>
  <tr><td class=fieldlabel>SpeciesAbbreviation</td><td><?= $sightingInfo["SpeciesAbbreviation"] ?></td></tr>
  <tr><td class=fieldlabel>LocationName</td><td><?= $sightingInfo["LocationName"] ?></td></tr>
  <tr><td class=fieldlabel>Notes</td><td><?= $sightingInfo["Notes"] ?></td></tr>
  <tr><td class=fieldlabel>Exclude</td><td><?= $sightingInfo["Exclude"] ?></td></tr>
  <tr><td class=fieldlabel>Photo</td><td><?= $sightingInfo["Photo"] ?></td></tr>
  <tr><td class=fieldlabel>TripDate</td><td><?= $sightingInfo["TripDate"] ?></td></tr>

  <tr><td class=heading colspan=2>Species</td></tr>
  <tr><td class=fieldlabel>Abbreviation</td><td><?= $speciesInfo["Abbreviation"] ?></td></tr>
  <tr><td class=fieldlabel>LatinName</td><td><?= $speciesInfo["LatinName"] ?></td></tr>
  <tr><td class=fieldlabel>CommonName</td><td><?= $speciesInfo["CommonName"] ?></td></tr>
  <tr><td class=fieldlabel>Notes</td><td><?= $speciesInfo["Notes"] ?></td></tr>
  <tr><td class=fieldlabel>ReferenceURL</td><td><?= $speciesInfo["ReferenceURL"] ?></td></tr>

  <tr><td class=heading colspan=2>Trip</td></tr>
  <tr><td class=fieldlabel>Name</td><td><?= $tripInfo["Name"] ?></td></tr>
  <tr><td class=fieldlabel>Leader</td><td><?= $tripInfo["Leader"] ?></td></tr>
  <tr><td class=fieldlabel>ReferenceURL</td><td><?= $tripInfo["ReferenceURL"] ?></td></tr>
  <tr><td class=fieldlabel>Name</td><td><?= $tripInfo["Name"] ?></td></tr>
  <tr><td class=fieldlabel>Notes</td><td><?= $tripInfo["Notes"] ?></td></tr>
  <tr><td class=fieldlabel>Date</td><td><?= $tripInfo["Date"] ?></td></tr>

  <tr><td class=heading colspan=2>Location</td></tr>
  <tr><td class=fieldlabel>Name</td><td><?= $locationInfo["Name"] ?></td></tr>
  <tr><td class=fieldlabel>Reference URL</td><td><?= $locationInfo["ReferenceURL"] ?></td></tr>
  <tr><td class=fieldlabel>City</td><td><?= $locationInfo["City"] ?></td></tr>
  <tr><td class=fieldlabel>County</td><td><?= $locationInfo["County"] ?></td></tr>
  <tr><td class=fieldlabel>State</td><td><?= $locationInfo["State"] ?></td></tr>
  <tr><td class=fieldlabel>Notes</td><td><?= $locationInfo["Notes"] ?></td></tr>
  <tr><td class=fieldlabel>LatLongSystem</td><td><?= $locationInfo["LatLongSystem"] ?></td></tr>
  <tr><td class=fieldlabel>Latitude</td><td><?= $locationInfo["Latitude"] ?></td></tr>
  <tr><td class=fieldlabel>Longitude</td><td><?= $locationInfo["Longitude"] ?></td></tr>
</table>

</div>
</body>
</html>
