
<?php

require("./birdwalker.php");
$speciesID = $_GET['id'];

$speciesInfo = getSpeciesInfo($speciesID);
$sightingQuery = performQuery("select * from sighting where sighting.SpeciesAbbreviation='" . $speciesInfo["Abbreviation"] . "' and Photo='1' order by TripDate desc");
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?> photos</title>
</head>

<body>

<?php globalMenu(); disabledBrowseButtons(); navTrailPhotos(); ?>

<div class="contentright">
  <div class="titleblock">
    <div class=pagetitle>
      <a href="./speciesdetail.php?id=<?php echo $speciesInfo["objectid"] ?>"><?php echo $speciesInfo["CommonName"] ?></a>
    </div>
  </div>


<?php

while ($sightingInfo = mysql_fetch_array($sightingQuery))
{
	$tripInfo = performOneRowQuery("select *, date_format(Date, '%W,  %M %e, %Y') as niceDate from trip where Date='" . $sightingInfo["TripDate"] . "'");
	$tripYear =  substr($tripInfo["Date"], 0, 4);
	$locationInfo = performOneRowQuery("select * from location where Name='" . $sightingInfo["LocationName"] . "'");

	echo "<div class=heading>";
	echo "<div class=pagesubtitle><a href=\"./tripdetail.php?id=" . $tripInfo["objectid"] ."\">" . $tripInfo["niceDate"] . "</a></div>";
	echo "<div class=metadata>";
	echo "<a href=\"./locationdetail.php?id=" . $locationInfo["objectid"] . "\">" . $locationInfo["Name"] . "</a>";
	if (getEnableEdit()) { echo "<div><a href=\"./sightingedit.php?id=" . $sightingInfo["objectid"] . "\">edit</a></div>"; }
	echo "</div>";

	echo "</div>";


	if ($sightingInfo["Photo"] == "1") {
		$photoFilename = getPhotoFilename($sightingInfo);

		list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename);
		echo "<img width=" . $width . " height=" . $height . "  src=\"" . getPhotoURLForSightingInfo($sightingInfo) . "\">";
	}
}

?>

</div>
</body>
</html>
