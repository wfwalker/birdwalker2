
<?php

require("./birdwalker.php");
require("./locationquery.php");

$minLat = param($_GET, "minlat", 37);
$maxLat = param($_GET, "maxlat", 37.5);
$minLong = param($_GET, "minlong", 121);
$maxLong = param($_GET, "maxlong", 121.5);

$longRange = $maxLong - $minLong;
$latRange = $maxLat - $minLat;
$longPan = $longRange * 0.1;
$latPan = $latRange * 0.3; 

$centerLong = ($minLong + $maxLong) / 2.0;
$centerLat = ($minLat + $maxLat) / 2.0;

function scale($srcMin, $srcMax, $srcVal, $destMin, $destMax)
{
	return $destMin + ($destMax - $destMin) * ($srcVal - $srcMin) / ($srcMax - $srcMin);
}

$view = param($_GET, "view", "");

$locationQuery = new LocationQuery;

$dbQuery = performQuery(
			$locationQuery->getSelectClause() . ", location.Latitude, location.Longitude" .
			$locationQuery->getFromClause() . " " . 
			$locationQuery->getWhereClause() . " AND Latitude>" . $minLat . " AND Latitude<" . $maxLat . " AND Longitude>" . $minLong . " AND Longitude<" . $maxLong . 
			" ORDER BY location.State, location.County, location.Name");


?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Scrolling Map</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailLocations();
?>

    <div class=contentright>



	<a href="./location-map.php?minlong=<?= $centerLong-0.6*$longRange ?>&maxlong=<?= $centerLong+0.6*$longRange ?>&minlat=<?=$centerLat-0.6*$latRange?>&maxlat=<?=$centerLat+0.6*$latRange?>">Out</a>
	<a href="./location-map.php?minlong=<?= $centerLong-0.4*$longRange ?>&maxlong=<?= $centerLong+0.4*$longRange ?>&minlat=<?=$centerLat-0.4*$latRange?>&maxlat=<?=$centerLat+0.4*$latRange?>">In</a>
	<p>&nbsp;</p>


	<div style="position: relative; border: 1px solid; background-image: url(http://terraserver.microsoft.com/ogcmap.ashx?version=1.1.1&request=GetMap&Layers=DOQ&Styles=&SRS=EPSG:4326&BBOX=-<?=$maxLong?>,<?=$minLat?>,-<?=$minLong?>,<?=$maxLat?>&width=500&height=500&format=image/jpeg&Exceptions=se_xml); height:500px; width: 500px;">

	<div style="position: absolute; left: 250px; top: -20px; color: red">
	  <a href="./location-map.php?minlat=<?= $minLat+$latPan ?>&maxlat=<?= $maxLat+$latPan ?>&minlong=<?=$minLong?>&maxlong=<?=$maxLong?>">N</a>
	</div>
	<div style="position: absolute; left: 250px; top: 510px; color: red">
	  <a href="./location-map.php?minlat=<?= $minLat-$latPan ?>&maxlat=<?= $maxLat-$latPan ?>&minlong=<?=$minLong?>&maxlong=<?=$maxLong?>">S</a>
	</div>
	<div style="position: absolute; left: -20px; top: 250px; color: red">
	  <a href="./location-map.php?minlong=<?= $minLong+$longPan ?>&maxlong=<?= $maxLong+$longPan ?>&minlat=<?=$minLat?>&maxlat=<?=$maxLat?>">W</a>
	</div>
	<div style="position: absolute; left: 510px; top: 250px; color: red">
	  <a href="./location-map.php?minlong=<?= $minLong-$longPan ?>&maxlong=<?= $maxLong-$longPan ?>&minlat=<?=$minLat?>&maxlat=<?=$maxLat?>">E</a>
	</div>
<?
	while($info = mysql_fetch_array($dbQuery))
	{
		$lat = $info["Latitude"];
		$top = round(scale($minLat, $maxLat, $lat, 500, 0));

		$long = $info["Longitude"];
		$left = round(scale($minLong, $maxLong, $long, 500, 0));

		if ($lat != 0)
		{
			echo "<div style=\"position: absolute; left: " . $left . "px; top: " . $top . "px; color: white\">";
						echo "<a href=\"./locationdetail.php?id=" . $info["objectid"] . "\">";
						echo "<img border=0 src=\"./images/first_hilite.gif\">";
			//echo "left: " . $left . ", top: " . $top;
						echo $info["Name"];
						echo "</a>";
			echo "</div>";
		}
	} ?>


    </div>
  </body>
</html>
