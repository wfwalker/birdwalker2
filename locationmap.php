
<?php

require("./birdwalker.php");
require("./locationquery.php");

$minLat = param($_GET, "minlat", 37.3);
$maxLat = param($_GET, "maxlat", 37.5);
$minLong = param($_GET, "minlong", 121.9);
$maxLong = param($_GET, "maxlong", 122.1);
$backgnd = param($_GET, "backgnd", "roads");

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
			" ORDER BY location.Latitude desc, location.Longitude");

$roads = "http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap?servicename=USGS_WMS_REF&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
	"-" . $maxLong . "," . $minLat. ",-" . $minLong . "," . $maxLat .
    "&WIDTH=500&HEIGHT=500&LAYERS=States,County_Labels,County,Route_Numbers,Roads,Streams,Names-Streams,Water_Bodies,Names-Water_Bodies,Urban_Areas,Federal_Lands,Names-Federal_Lands&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

$landcover =  "http://ims.cr.usgs.gov/servlet/com.esri.wms.Esrimap?WMTVER=1.0.0&LAYERS=US_NLCD&FORMAT=PNG&BGCOLOR=0x000000&TRANSPARENT=true&SRS=EPSG:4326&SERVICE=WMS&STYLES=&SERVICENAME=USGS_EDC_LandCover_NLCD&BBOX=" . 
	"-" . $maxLong . "," . $minLat. ",-" . $minLong . "," . $maxLat .
    "&REQUEST=map&WIDTH=500&HEIGHT=500&LAYERS=US_NED_Shaded_Relief&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

$elevation = "http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap?servicename=USGS_WMS_NED&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
	"-" . $maxLong . "," . $minLat. ",-" . $minLong . "," . $maxLat .
    "&WIDTH=500&HEIGHT=500&LAYERS=US_NED_Shaded_Relief&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

$terraserver =
    "http://terraserver.microsoft.com/ogcmap.ashx?version=1.1.1&request=GetMap&Layers=DOQ&Styles=GeoGrid&SRS=EPSG:4326&BBOX=" .
	"-" . $maxLong . "," . $minLat. ",-" . $minLong . "," . $maxLat .
    "&width=500&height=500&format=image/jpeg&Exceptions=se_xml";

if ($backgnd == "roads") $backgndURL = $roads;
else if ($backgnd == "elevation") $backgndURL = $elevation;
else if ($backgnd == "landcover") $backgndURL = $landcover;
else $backgndURL = $terraserver;

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | OpenGIS</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailLocations();
?>

    <div class=contentright>
	<div><?= round($latRange * 69.0) ?> miles</div>
	<div style="position: relative; border: 1px solid; background-image: url(<?=$backgndURL?>); height:500px; width: 500px;">

	<div style="position: absolute; left: 250px; top: -20px; color: red">
	  <a href="./locationmap.php?inlat=<?= $minLat+$latPan ?>&maxlat=<?= $maxLat+$latPan ?>&minlong=<?=$minLong?>&maxlong=<?=$maxLong?>&backgnd=<?=$backgnd?>">N</a>
	</div>
	<div style="position: absolute; left: 250px; top: 505px; color: red">
	  <a href="./locationmap.php?minlat=<?= $minLat-$latPan ?>&maxlat=<?= $maxLat-$latPan ?>&minlong=<?=$minLong?>&maxlong=<?=$maxLong?>&backgnd=<?=$backgnd?>">S</a>
	</div>
	<div style="position: absolute; left: -20px; top: 250px; color: red">
	  <a href="./locationmap.php?minlong=<?= $minLong+$longPan ?>&maxlong=<?= $maxLong+$longPan ?>&minlat=<?=$minLat?>&maxlat=<?=$maxLat?>&backgnd=<?=$backgnd?>">W</a>
	</div>
	<div style="position: absolute; left: 510px; top: 250px; color: red">
	  <a href="./locationmap.php?minlong=<?= $minLong-$longPan ?>&maxlong=<?= $maxLong-$longPan ?>&minlat=<?=$minLat?>&maxlat=<?=$maxLat?>&backgnd=<?=$backgnd?>">E</a>
	</div>
<?

	$counter = 1;

	while($info = mysql_fetch_array($dbQuery))
	{
		$lat = $info["Latitude"];
		$top = round(scale($minLat, $maxLat, $lat, 500, 0));

		$long = $info["Longitude"];
		$left = round(scale($minLong, $maxLong, $long, 500, 0));

		if ($lat != 0)
		{
			$locationInfo[$counter] = $info;
			$mylat = $info["Latitude"];
			$mylong = $info["Longitude"];

			?><div style="position: absolute; left: <?= $left ?>px; top: <?= $top ?>px" nowrap>
				   <a href="./locationmap.php?backgnd=<?= $backgnd ?>&minlat=<?= $mylat - 0.25*$latRange?>&maxlat=<?= $mylat + 0.25*$latRange?>&minlong=<?= $mylong - 0.25*$longRange?>&maxlong=<?= $mylong + 0.25*$longRange?>" style="color: white; background-color: red; padding-left: 3px; padding-right: 3px">
					    <?= $counter ?>
				   </a>
			</div><?

			$counter++;
		}
	}

?>

   </div>

   <div style="position: relative; left: 530px; top: -500px ">
     <p>
       <a href="./locationmap.php?minlong=<?= $centerLong-0.6*$longRange ?>&maxlong=<?= $centerLong+0.6*$longRange ?>&minlat=<?=$centerLat-0.6*$latRange?>&maxlat=<?=$centerLat+0.6*$latRange?>&backgnd=<?=$backgnd?>">out</a> |
	   <a href="./locationmap.php?minlong=<?= $centerLong-0.4*$longRange ?>&maxlong=<?= $centerLong+0.4*$longRange ?>&minlat=<?=$centerLat-0.4*$latRange?>&maxlat=<?=$centerLat+0.4*$latRange?>&backgnd=<?=$backgnd?>">in</a> |
	   <a href="./locationmap.php?view=<?= $view ?>&minlong=<?= $minLong?>&maxlong=<?= $maxLong?>&minlat=<?= $minLat?>&maxlat=<?= $maxLat?>&backgnd=elevation">elevation</a> |
	   <a href="./locationmap.php?view=<?= $view ?>&minlong=<?= $minLong?>&maxlong=<?= $maxLong?>&minlat=<?= $minLat?>&maxlat=<?= $maxLat?>&backgnd=terraserver">photo</a> |
	   <a href="./locationmap.php?view=<?= $view ?>&minlong=<?= $minLong?>&maxlong=<?= $maxLong?>&minlat=<?= $minLat?>&maxlat=<?= $maxLat?>&backgnd=landcover">landcover</a> |
	   <a href="./locationmap.php?view=<?= $view ?>&minlong=<?= $minLong?>&maxlong=<?= $maxLong?>&minlat=<?= $minLat?>&maxlat=<?= $maxLat?>&backgnd=roads">roads</a>
     </p>

<?
   for($i = 1; $i < $counter; $i++)
   {
	   ?><div><?= $i ?>. <a href="./locationdetail.php?id=<?= $locationInfo[$i]["objectid"] ?>"><?= $locationInfo[$i]["Name"] ?></a></div><?
   }
?>
   </div>


   </div>
  </body>
</html>
