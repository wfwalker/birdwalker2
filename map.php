
<?php

require_once("./locationquery.php");

class Map
{
	var $mPageURL;
	var $mMapHeight;
	var $mMapWidth;

	var $mMinumumLatitude;
	var $mMaxumumLatitude;
	var $mMinumumLongitude;
	var $mMaxumumLongitude;
	var $mBackground;
	var $mLocationQuery;

	function Map($inPageURL)
	{
		$this->mLocationQuery = new LocationQuery;
		$this->mMapHeight = 500;
		$this->mMapWidth = 500;
		$this->mPageURL = $inPageURL;
	}

	function setMinimumLatitude($inValue) { $this->mMinimumLatitude = $inValue; }
	function setMaximumLatitude($inValue) { $this->mMaximumLatitude = $inValue; }
	function setMinimumLongitude($inValue) { $this->mMinimumLongitude = $inValue; }
	function setMaximumLongitude($inValue) { $this->mMaximumLongitude = $inValue; }
	function setBackground($inValue) { $this->mBackground = $inValue; }

	function setFromRequest($get)
	{
		$this->mLocationQuery->setFromRequest($_GET);

		if ($_GET["minlat"] == "")
		{
			$extrema = $this->mLocationQuery->findExtrema();

			$this->setMinimumLatitude($extrema["minLat"]);
			$this->setMaximumLatitude($extrema["maxLat"]);
			$this->setMinimumLongitude($extrema["minLong"]);
			$this->setMaximumLongitude($extrema["maxLong"]);
			$this->setBackground(param($_GET, "backgnd", "roads"));
		}
		else
		{
			$this->setMinimumLatitude(param($_GET, "minlat", 37.3));
			$this->setMaximumLatitude(param($_GET, "maxlat", 37.5));
			$this->setMinimumLongitude(param($_GET, "minlong", 121.9));
			$this->setMaximumLongitude(param($_GET, "maxlong", 122.1));
			$this->setBackground(param($_GET, "backgnd", "roads"));
		}

		echo "<!-- " . $this->mMinimumLongitude . ", " . $this->mMinimumLatitude . ", " . $this->mMaximumLongitude . ", " . $this->mMaximumLatitude . " -->\n";
	}

	function scaleLat($inLat)
	{
		return $this->mMapHeight - $this->mMapHeight * ($inLat - $this->mMinimumLatitude) /
			($this->mMaximumLatitude - $this->mMinimumLatitude);
	}

	function scaleLong($inLong)
	{
		return $this->mMapWidth - $this->mMapWidth * ($inLong - $this->mMinimumLongitude) /
			($this->mMaximumLongitude - $this->mMinimumLongitude);
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
			"-" . $this->mMaximumLongitude . "," . $this->mMinimumLatitude. ",-" . $this->mMinimumLongitude . "," . $this->mMaximumLatitude .
			"&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			"&LAYERS=States,County_Labels,County,Route_Numbers,Roads,Streams,Names-Streams,Water_Bodies,Names-Water_Bodies,Urban_Areas,Federal_Lands,Names-Federal_Lands&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		$landcover =
			 "http://ims.cr.usgs.gov/servlet/com.esri.wms.Esrimap?" . 
			 "WMTVER=1.0.0&LAYERS=US_NLCD&FORMAT=PNG&BGCOLOR=0x000000&TRANSPARENT=true&SRS=EPSG:4326&SERVICE=WMS&STYLES=&SERVICENAME=USGS_EDC_LandCover_NLCD&BBOX=" . 
			 "-" . $this->mMaximumLongitude . "," . $this->mMinimumLatitude. ",-" . $this->mMinimumLongitude . "," . $this->mMaximumLatitude .
			 "&REQUEST=map" . 
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			 "&LAYERS=US_NED_Shaded_Relief&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		$relief2 =
			 "http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap/USGS_WMS_GTOPO?" . 
			 "LAYERS=GTOPO60%20Color%20Shaded%20Relief&FORMAT=gif&REQUEST=GetMap&SRS=EPSG:4326&servicename=WMS&EXCEPTIONS=INIMAGE&BBOX=" . 
			 "-" . $this->mMaximumLongitude . "," . $this->mMinimumLatitude. ",-" . $this->mMinimumLongitude . "," . $this->mMaximumLatitude .
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight;

		$relief =
			 "http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap?" .
			 "servicename=USGS_WMS_NED&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
			 "-" . $this->mMaximumLongitude . "," . $this->mMinimumLatitude. ",-" . $this->mMinimumLongitude . "," . $this->mMaximumLatitude .
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			 "&LAYERS=US_NED_Shaded_Relief&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		$landsat = 
			 "http://ims.cr.usgs.gov:80/servlet/com.esri.wms.Esrimap/USGS_WMS_LANDSAT7?" . 
			 "servicename=WMS&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
			 "-" . $this->mMaximumLongitude . "," . $this->mMinimumLatitude. ",-" . $this->mMinimumLongitude . "," . $this->mMaximumLatitude .
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			 "&LAYERS=LANDSAT7&STYLES=reference&FORMAT=GIF&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		$terraserver =
			 "http://terraserver.microsoft.com/ogcmap.ashx?" .
			 "version=1.1.1&request=GetMap&Layers=DOQ&Styles=GeoGrid&SRS=EPSG:4326&BBOX=" .
			 "-" . $this->mMaximumLongitude . "," . $this->mMinimumLatitude. ",-" . $this->mMinimumLongitude . "," . $this->mMaximumLatitude .
			 "&WIDTH=" . $this->mMapWidth . "&HEIGHT=" . $this->mMapHeight .
			 "&format=image/jpeg&Exceptions=se_xml";

		if ($this->mBackground == "roads") return $roads;
		else if ($this->mBackground == "relief") return $relief2;
		else if ($this->mBackground == "landcover") return $landcover;
		else return $terraserver;
		//		else return $terraserver;
	}

	function linkToSelf($minLat, $maxLat, $minLong, $maxLong, $backgnd, $anchorText, $style = "")
	{
		return "<a href=\"" . $this->mPageURL . "?" .
			"view=map&" . 
			"minlat=" . $minLat . "&maxlat=" . $maxLat .
			"&minlong=" . $minLong . "&maxlong=" . $maxLong . 
			"&backgnd=" . $backgnd .
			$this->mLocationQuery->getParams() . "\"" . 
			" style=\"" . $style . "\">" .
			$anchorText . 
			"</a>";
	}

	function linkToSelfChangeBackground($background)
	{
		return $this->linkToSelf($this->mMinimumLatitude, $this->mMaximumLatitude, $this->mMinimumLongitude, $this->mMaximumLongitude,
								 $background, $background);
		
	}

	function linkToSelfZoom($zoomFactor, $anchortext)
	{
		$centerLong = ($this->mMinimumLongitude + $this->mMaximumLongitude) / 2.0;
		$centerLat = ($this->mMinimumLatitude + $this->mMaximumLatitude) / 2.0;
		$longRange = $this->mMaximumLongitude - $this->mMinimumLongitude;
		$latRange = $this->mMaximumLatitude - $this->mMinimumLatitude;

		return $this->linkToSelf($centerLat - $zoomFactor * $latRange, $centerLat + $zoomFactor * $latRange,
								 $centerLong - $zoomFactor * $longRange, $centerLong + $zoomFactor * $longRange,
								 $this->mBackground, $anchortext);
	}

	function linkToSelfPan($latPan, $longPan, $anchortext)
	{
		return $this->linkToSelf($this->mMinimumLatitude + $latPan, $this->mMaximumLatitude + $latPan, 
								 $this->mMinimumLongitude + $longPan, $this->mMaximumLongitude + $longPan,
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
		$longRange = $this->mMaximumLongitude - $this->mMinimumLongitude;
		$latRange = $this->mMaximumLatitude - $this->mMinimumLatitude;
		$longPan = $longRange * 0.25;
		$latPan = $latRange * 0.25; 
?>
	<div style="position: absolute; left: <?= $this->mMapWidth / 2 ?>px; top: -20px">
		 <?= $this->linkToSelfPan($latPan, 0, "N"); ?>
	</div>
	<div style="position: absolute; left: <?= $this->mMapWidth / 2 ?>px; top: <?= $this->mMapWidth + 5 ?>px">
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
		$longRange = $this->mMaximumLongitude - $this->mMinimumLongitude;
		$latRange = $this->mMaximumLatitude - $this->mMinimumLatitude;
		$longPan = $longRange * 0.1;
		$latPan = $latRange * 0.3; 
		
		$centerLong = ($this->mMinimumLongitude + $this->mMaximumLongitude) / 2.0;
		$centerLat = ($this->mMinimumLatitude + $this->mMaximumLatitude) / 2.0;


?>

	   <div>
		 <?= $this->linkToSelfZoom(0.6, "out"); ?> |
		 <?= $this->linkToSelfZoom(0.4, "in"); ?> |
	     <?= round($latRange * 69.0) ?> miles
       </div>
	   
       <div style="position: relative; border: 1px solid gray; background-image: url(<?=$this->getBackgroundImageURL()?>); height:<?= $this->mMapHeight ?>px; width: <?= $this->mMapWidth?>px;">

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

				<?=  $this->linkToSelf($mylat - 0.25 * $latRange, $mylat + 0.25 * $latRange,
									   $mylong - 0.25 * $longRange, $mylong + 0.25 * $longRange,
									   $this->mBackground,
									   $counter,
									   "color: white; border: 1px solid black; background-color: blue; padding-left: 3px; padding-right: 3px"); ?>
			   </div>
<?
			   $counter++;
			}
		}
?>

   </div>

   <div style="position: relative; width: 300px; left: <?= $this->mMapWidth + 30?>px; top: -<?= $this->mMapHeight?>px ">
	
         <p><? $this->drawLayerControls(); ?></p>

<?
		 for($i = 1; $i < $counter; $i++)
		 { ?>
			 <div style="padding: 2px">
                 <span style="color: white; border: 1px solid black; background-color: blue; padding-left: 3px; padding-right: 3px"><?= $i ?></span>
				 <a href="./locationdetail.php?id=<?= $locationInfo[$i]["objectid"] ?>"><?= $locationInfo[$i]["Name"] ?></a>
			 </div>
<?		 } ?>

    </div>

<? }
}

?>