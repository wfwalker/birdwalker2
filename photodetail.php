
<?php

require("./birdwalker.php");
$sightingID = $_GET['id'];

$sightingInfo = getSightingInfo($sightingID);
$speciesInfo = performOneRowQuery("select * from species where Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
$tripInfo = performOneRowQuery("select *, date_format(Date, '%W,  %M %e, %Y') as niceDate from trip where Date='" . $sightingInfo["TripDate"] . "'");
$tripYear =  substr($tripInfo["Date"], 0, 4);
$locationInfo = performOneRowQuery("select * from location where Name='" . $sightingInfo["LocationName"] . "'");

$firstPhotoSightingID = performCount("select min(objectid) from sighting where Photo='1'");
$lastPhotoSightingID = performCount("select max(objectid) from sighting where Photo='1'");
$nextPhotoSightingID = performCount("select min(objectid) from sighting where Photo='1' and objectid > " . $sightingID);
$prevPhotoSightingID = performCount("select max(objectid) from sighting where Photo='1' and objectid < " . $sightingID);

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?>,  <?php echo $tripInfo["niceDate"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

    <div class="navigationleft">
	  <a href="./photodetail.php?id=<?php echo $firstPhotoSightingID ?>">first</a>
	  <a href="./photodetail.php?id=<?php echo $prevPhotoSightingID ?>">prev</a>
      <a href="./photodetail.php?id=<?php echo $nextPhotoSightingID ?>">next</a>
      <a href="./photodetail.php?id=<?php echo $lastPhotoSightingID ?>">last</a>
    </div>


<div class="contentright">
<div class="titleblock">
	  <div class=pagetitle><a href="./speciesdetail.php?id=<?php echo $speciesInfo["objectid"] ?>"><?php echo $speciesInfo["CommonName"] ?></a></div>
      <div class=pagesubtitle><a href="./tripdetail.php?id=<?php echo $tripInfo["objectid"] ?>"><?php echo $tripInfo["niceDate"] ?></div>
      <div class=metadata>
        <a href="./countydetail.php?county=<?php echo $locationInfo["County"] ?>"><?php echo $locationInfo["County"] ?> County</a>,
        <a href="./statedetail.php?state=<?php echo $locationInfo["State"] ?>"><?php echo getStateNameForAbbreviation($locationInfo["State"]) ?></a>
      </div>
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
