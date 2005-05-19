
<?php

require_once("./locationquery.php");

class Map
{
	var $mPageURL;
	var $mMapHeight;
	var $mMapWidth;

	var $mLatitude;
	var $mLongitude;
	var $mScale;

	var $mBackground;
	var $mLocationQuery;

	function Map($inPageURL)
	{
		$this->mLocationQuery = new LocationQuery;
		$this->mMapHeight = 320;
		$this->mMapWidth = 640;
		$this->mPageURL = $inPageURL;
	}

	function setLatitude($inValue) { $this->mLatitude = $inValue; }
	function setLongitude($inValue) { $this->mLongitude = $inValue; }
	function setScale($inValue) { $this->mScale = $inValue; }
	function setBackground($inValue) { $this->mBackground = $inValue; }

	function getMinimumLatitude() { return $this->mLatitude - $this->getLatitudeRadius(); }
	function getMaximumLatitude() { return $this->mLatitude + $this->getLatitudeRadius(); }
	function getMinimumLongitude() { return $this->mLongitude - $this->getLongitudeRadius(); }
	function getMaximumLongitude() { return $this->mLongitude + $this->getLongitudeRadius(); }
	function getLatitudeRadius() { return $this->mScale; }
	function getLongitudeRadius() { return $this->mScale * ($this->mMapWidth / $this->mMapHeight); }

	function setFromRequest($get)
	{
		$this->mLocationQuery->setFromRequest($_GET);

		if ($_GET["lat"] == "") // get parameters from location query
		{
			$extrema = $this->mLocationQuery->findExtrema();

			// put the map in the center of the extrema
			$this->setLatitude(($extrema["minLat"] + $extrema["maxLat"]) / 2.0);
			$this->setLongitude(($extrema["minLong"] + $extrema["maxLong"]) / 2.0);
			$this->setBackground(param($_GET, "backgnd", "roads"));

			// compute lat and long ranges, with a lower bound in case there's only one location in the set
			$longRange = max(0.25, abs($extrema["maxLong"] - $extrema["minLong"]));
			$latRange = max(0.25, abs($extrema["maxLat"] - $extrema["minLat"]));

			// using the aspect ratio, decide on how to scale the map so it fits all the points
			$minRange = max(0.25 , min($latRange, $longRange * ($this->mMapWidth / $this->mMapHeight)));
			$this->setScale(0.75 * $minRange);
		}
		else // get parameters from query
		{
			$this->setLatitude(param($_GET, "lat", 37.3));
			$this->setLongitude(param($_GET, "long", -121.9));
			$this->setScale(param($_GET, "scale", 1.0));
			$this->setBackground(param($_GET, "backgnd", "roads"));
		}

		echo "<!-- '" . $this->mLongitude . "', '" . $this->mLatitude . "', '" . $this->mScale . "' -->\n";
		echo "<!-- '" . $this->getLatitudeRadius() . "', '" . $this->getLongitudeRadius() . "' -->\n";
	}


	function scaleLat($inLat)
	{
		return $this->mMapHeight - $this->mMapHeight * ($inLat - $this->getMinimumLatitude()) /
			($this->getMaximumLatitude() - $this->getMinimumLatitude());
	}

	function scaleLong($inLong)
	{
		return $this->mMapWidth * ($inLong - $this->getMinimumLongitude()) /
			($this->getMaximumLongitude() - $this->getMinimumLongitude());
	}

	function performDBQuery()
	{
		return performQuery(
							$this->mLocationQuery->getSelectClause() . ", location.Latitude, location.Longitude" .
							$this->mLocationQuery->getFromClause() . " " . 
							$this->mLocationQuery->getWhereClause() .
							" ORDER BY location.Latitude desc, location.Longitude");
	}

	function getBackgroundImageURL()
	{
		$roads =
			"http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap?" . 
			"servicename=USGS_WMS_REF&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
			$this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			"&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			"&LAYERS=States,County_Labels,County,Route_Numbers,Roads,Streams,Names-Streams,Water_Bodies,Names-Water_Bodies,Urban_Areas,Federal_Lands,Names-Federal_Lands&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		$landcover =
			 "http://ims.cr.usgs.gov/servlet/com.esri.wms.Esrimap?" . 
			 "WMTVER=1.0.0&LAYERS=US_NLCD&FORMAT=PNG&BGCOLOR=0x000000&TRANSPARENT=true&SRS=EPSG:4326&SERVICE=WMS&STYLES=&SERVICENAME=USGS_EDC_LandCover_NLCD&BBOX=" . 
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&REQUEST=map" . 
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			 "&LAYERS=US_NED_Shaded_Relief&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		// nice shaded color relief but low res
		$relief2 =
			 "http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap/USGS_WMS_GTOPO?" . 
			 "LAYERS=GTOPO60%20Color%20Shaded%20Relief&FORMAT=gif&REQUEST=GetMap&SRS=EPSG:4326&servicename=WMS&EXCEPTIONS=INIMAGE&BBOX=" . 
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight;

		// higher res elevation but greyscale only
		$relief =
			 "http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap?" .
			 "servicename=USGS_WMS_NED&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			 "&LAYERS=US_NED_Shaded_Relief&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		// medium res landsat
		$landsat = 
			 "http://ims.cr.usgs.gov:80/servlet/com.esri.wms.Esrimap/USGS_WMS_LANDSAT7?" . 
			 "servicename=WMS&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			 "&LAYERS=LANDSAT7&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		// seemless high res b/w photo
		$terraserver =
			 "http://terraserver.microsoft.com/ogcmap.ashx?" .
			 "version=1.1.1&request=GetMap&Layers=DOQ&Styles=GeoGrid&SRS=EPSG:4326&BBOX=" .
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			 "&format=image/jpeg&Exceptions=se_xml";

		if ($this->mBackground == "roads") return $roads;
		else if ($this->mBackground == "relief") return $relief2;
		else if ($this->mBackground == "landcover") return $landcover;
		else return $terraserver;
		//		else return $terraserver;
	}

	function linkToSelf($lat, $long, $scale, $backgnd, $anchorText, $style = "")
	{
		return "<a href=\"" . $this->mPageURL . "?" .
			"view=map&" . 
			"lat=" . $lat . "&long=" . $long . "&scale=" . $scale .
			"&backgnd=" . $backgnd .
			$this->mLocationQuery->getParams() . "\"" . 
			" class=\"" . $style . "\">" .
			$anchorText . 
			"</a>";
	}

	function linkToSelfChangeBackground($background)
	{
		return $this->linkToSelf($this->mLatitude, $this->mLongitude, $this->mScale,
								 $background, $background);
		
	}

	function linkToSelfZoom($zoomFactor, $anchortext)
	{
		return $this->linkToSelf($this->mLatitude, $this->mLongitude, $this->mScale * $zoomFactor,
								 $this->mBackground, $anchortext);
	}

	function linkToSelfPan($latPan, $longPan, $anchortext)
	{
		return $this->linkToSelf($this->mLatitude + $latPan, $this->mLongitude + $longPan, $this->mScale,
								 $this->mBackground, $anchortext);
	}

	function drawLayerControls()
	{ ?>
		 <?= $this->linkToSelfChangeBackground("relief"); ?> |
		 <?= $this->linkToSelfChangeBackground("photo"); ?> |
		 <?= $this->linkToSelfChangeBackground("landcover"); ?> |
		 <?= $this->linkToSelfChangeBackground("roads"); ?>
<?	}

	function drawPanControls()
	{
		$longPan = -$this->getLongitudeRadius() * 0.25;
		$latPan = $this->getLatitudeRadius() * 0.25; 
		echo "<!-- PAN " . $longPan . ", " . $latPan . " -->";
?>
	<div style="position: absolute; left: <?= $this->mMapWidth / 2 ?>px; top: -20px">
		 <?= $this->linkToSelfPan($latPan, 0, "N"); ?>
	</div>
	<div style="position: absolute; left: <?= $this->mMapWidth / 2 ?>px; top: <?= $this->mMapHeight + 5 ?>px">
		 <?= $this->linkToSelfPan(-$latPan, 0, "S"); ?>
	</div>
	<div style="position: absolute; left: -20px; top: <?= $this->mMapHeight / 2 ?>px">
		 <?= $this->linkToSelfPan(0, $longPan, "W"); ?>
	</div>
	<div style="position: absolute; left: <?= $this->mMapWidth + 10 ?>px; top: <?= $this->mMapHeight / 2 ?>px">
		 <?= $this->linkToSelfPan(0, -$longPan, "E"); ?>
	</div>
<?
	}


	function draw()
	{
		$centerLong = ($this->mMinimumLongitude + $this->mMaximumLongitude) / 2.0;
		$centerLat = ($this->mMinimumLatitude + $this->mMaximumLatitude) / 2.0;

		$minRange = min($this->mMaximumLongitude - $this->mMinimumLongitude, $this->mMaximumLatitude - $this->mMinimumLatitude);
		$minPixels = min($this->mMapWidth, $this->mMapHeight);

		$longRange = $minRange * $this->mMapHeight / $minPixels;
		$latRange = $minRange * $this->mMapWidth / $minPixels;

		$longPan = $longRange * 0.1;
		$latPan = $latRange * 0.3; 
		
?>


	   <div style="text-align: right; padding-top: 30px;">
		 <?= $this->linkToSelfZoom(1.2, "out"); ?> | <?= $this->linkToSelfZoom(0.8, "in"); ?> | <?= $this->drawLayerControls(); ?>
       </div>

       <div id="theMap" style="position: relative; border: 1px solid gray; background-image: url(./images/loading.gif); height:<?= $this->mMapHeight ?>px; width: <?= $this->mMapWidth?>px;">

<script language = javascript>
document.getElementById('theMap').style.background = "url('<?=$this->getBackgroundImageURL()?>')";
</script>

<?
		$this->drawPanControls();

		$dbQuery = $this->performDBQuery();

		$counter = 1;

		while($info = mysql_fetch_array($dbQuery))
		{
			$lat = $info["Latitude"];
			$top = round($this->scaleLat($lat)) - 8;
			
			$long = $info["Longitude"];
			$left = round($this->scaleLong($long)) - 8; 

			// TODO, check for points out of range
			
			if (($left > 0) && ($left < $this->mMapWidth) && ($top > 0) && ($top < $this->mMapHeight))
			{
				$locationInfo[$counter] = $info;
				$mylat = $info["Latitude"];
				$mylong = $info["Longitude"]; ?>
				
			    <div style="position: absolute; left: <?= $left ?>px; top: <?= $top ?>px" nowrap>

					 <?=  $this->linkToSelf($mylat, $mylong, $this->mScale * 0.5,
									   $this->mBackground,
									   "+<span>" . $info["Name"] . "</span>",
									   "info"); ?>
			   </div>
<?
			   $counter++;
			}
		}
?>

   </div>
  

	 <p>&nbsp;</p>

<?
	countHeading($this->mLocationQuery->getLocationCount(), "location");
	$this->mLocationQuery->formatTwoColumnLocationList("map", true);

   }
}

?>