
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
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?>,  <?php echo $tripInfo["niceDate"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

<div class="contentright">
<div class="titleblock">
	  <div class=pagetitle> <?php echo $speciesInfo["CommonName"] ?></div>
      <div class=pagesubtitle><?php echo $tripInfo["niceDate"] ?></div>
      <div class=metadata><?php echo $locationInfo["County"] ?> County, <?php echo getStateNameForAbbreviation($locationInfo["State"]) ?></div>
</div>

<?php

if ($sightingInfo["Photo"] == "1") { echo "<img src=\"" . getPhotoURLForSightingInfo($sightingInfo) . "\">"; }

if (strlen($sightingInfo["Notes"]) > 0) {
  echo "<p class=sighting-notes>" . $sightingInfo["Notes"] . "</p>";
}

if (strlen($tripInfo["Notes"]) > 0) {
	echo "<div class=titleblock>" . $tripInfo["Name"] . "</div>";
	echo "<p class=sighting-notes>" . $tripInfo["Notes"] . "</p>";
}

if (strlen($locationInfo["Notes"]) > 0) {
	echo "<div class=titleblock>" . $locationInfo["Name"] . "</div>";
	echo "<p class=sighting-notes>" . $locationInfo["Notes"] . "</p>";
}

if (strlen($speciesInfo["Notes"]) > 0) {
	echo "<div class=titleblock>" . $speciesInfo["CommonName"] . "</div>";
	echo "<p class=sighting-notes>" . $speciesInfo["Notes"] . "</p>";
}

?>

</div>
</body>
</html>
