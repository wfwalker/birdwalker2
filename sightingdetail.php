
<?php

require("./birdwalker.php");
$sightingID = $_GET['id'];
$sightingInfo = getSightingInfo($sightingID);
$speciesInfo = performOneRowQuery("select * from species where Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
$tripInfo = performOneRowQuery("select *, date_format(Date, '%W,  %M %e, %Y') as niceDate from trip where Date='" . $sightingInfo["TripDate"] . "'");
$tripYear =  substr($tripInfo["Date"], 0, 4);
$locationInfo = performOneRowQuery("select * from location where Name='" . $sightingInfo["LocationName"] . "'");
$firstSightings = getFirstSightings();
$firstYearSightings = getFirstYearSightings($tripYear);
$sightingCount = performCount("select count(*) from sighting");
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?>,  <?php echo $tripInfo["niceDate"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

    <div class="navigationleft">
	  <a href="./sightingdetail.php?id=1">first</a>
	  <a href="./sightingdetail.php?id=<?php echo $_GET['id'] - 1 ?>">prev</a>
      <a href="./sightingdetail.php?id=<?php echo $_GET['id'] + 1 ?>">next</a>
      <a href="./sightingdetail.php?id=<?php echo $sightingCount ?>">last</a>
    </div>

<div class="contentright">
<div class="titleblock">
	  <div class=pagetitle> <?php echo $speciesInfo["CommonName"] ?></div>
      <div class=pagesubtitle><?php echo $tripInfo["niceDate"] ?></div>
      <div class=metadata><?php echo $locationInfo["County"] ?> County, <?php echo getStateNameForAbbreviation($locationInfo["State"]) ?></div>
</div>

<?php if ($sightingInfo["Photo"] == "1") { echo "<a href=\"". getPhotoURLForSightingInfo($sightingInfo) . "\"><img src=\"" . getPhotoThumbURLForSightingInfo($sightingInfo) . "\"></a>"; } ?>

<table class=report-content columns=2 width=100%>
  <tr><td class=titleblock colspan=2>Sighting</td></tr>
  <tr><td class=fieldlabel>SpeciesAbbreviation</td><td><?php echo $sightingInfo["SpeciesAbbreviation"] ?></td></tr>
  <tr><td class=fieldlabel>LocationName</td><td><?php echo $sightingInfo["LocationName"] ?></td></tr>
  <tr><td class=fieldlabel>Notes</td><td><?php echo $sightingInfo["Notes"] ?></td></tr>
  <tr><td class=fieldlabel>Exclude</td><td><?php echo $sightingInfo["Exclude"] ?></td></tr>
  <tr><td class=fieldlabel>Photo</td><td><?php echo $sightingInfo["Photo"] ?></td></tr>
  <tr><td class=fieldlabel>TripDate</td><td><?php echo $sightingInfo["TripDate"] ?></td></tr>

  <tr><td class=titleblock colspan=2>Species</td></tr>
  <tr><td class=fieldlabel>Abbreviation</td><td><?php echo $speciesInfo["Abbreviation"] ?></td></tr>
  <tr><td class=fieldlabel>LatinName</td><td><?php echo $speciesInfo["LatinName"] ?></td></tr>
  <tr><td class=fieldlabel>CommonName</td><td><?php echo $speciesInfo["CommonName"] ?></td></tr>
  <tr><td class=fieldlabel>Notes</td><td><?php echo $speciesInfo["Notes"] ?></td></tr>
  <tr><td class=fieldlabel>ReferenceURL</td><td><?php echo $speciesInfo["ReferenceURL"] ?></td></tr>

  <tr><td class=titleblock colspan=2>Trip</td></tr>
  <tr><td class=fieldlabel>Name</td><td><?php echo $tripInfo["Name"] ?></td></tr>
  <tr><td class=fieldlabel>Leader</td><td><?php echo $tripInfo["Leader"] ?></td></tr>
  <tr><td class=fieldlabel>ReferenceURL</td><td><?php echo $tripInfo["ReferenceURL"] ?></td></tr>
  <tr><td class=fieldlabel>Name</td><td><?php echo $tripInfo["Name"] ?></td></tr>
  <tr><td class=fieldlabel>Notes</td><td><?php echo $tripInfo["Notes"] ?></td></tr>
  <tr><td class=fieldlabel>Date</td><td><?php echo $tripInfo["Date"] ?></td></tr>

  <tr><td class=titleblock colspan=2>Location</td></tr>
  <tr><td class=fieldlabel>Name</td><td><?php echo $locationInfo["Name"] ?></td></tr>
  <tr><td class=fieldlabel>Reference URL</td><td><?php echo $locationInfo["ReferenceURL"] ?></td></tr>
  <tr><td class=fieldlabel>City</td><td><?php echo $locationInfo["City"] ?></td></tr>
  <tr><td class=fieldlabel>County</td><td><?php echo $locationInfo["County"] ?></td></tr>
  <tr><td class=fieldlabel>State</td><td><?php echo $locationInfo["State"] ?></td></tr>
  <tr><td class=fieldlabel>Notes</td><td><?php echo $locationInfo["Notes"] ?></td></tr>
  <tr><td class=fieldlabel>LatLongSystem</td><td><?php echo $locationInfo["LatLongSystem"] ?></td></tr>
  <tr><td class=fieldlabel>Latitude</td><td><?php echo $locationInfo["Latitude"] ?></td></tr>
  <tr><td class=fieldlabel>Longitude</td><td><?php echo $locationInfo["Longitude"] ?></td></tr>
</table>

</div>
</body>
</html>
