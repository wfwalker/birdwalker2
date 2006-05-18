
<?php

require_once("./locationquery.php");

class Map
{
	var $mLocationQuery;
	var $mReq;
	var $mPageURL;

	function Map($inPageURL, $inReq)
	{
		$this->mPageURL = $inPageURL;
		$this->mReq = $inReq;
		$this->mLocationQuery = new LocationQuery($inReq);

        if ($this->mReq->getLatitude() == "0") // special case, show whole united states
		{
		    $this->mReq->setLatitude(38);
		    $this->mReq->setLongitude(-96);
		    $this->mReq->setBackground($inReq->getBackground());
		    $this->mReq->setScale(15);
		}
		else if ($this->mReq->getLatitude() == "") // get parameters from location query
		{
 			$extrema = $this->mLocationQuery->findExtrema();

			// put the map in the center of the extrema
			$this->mReq->setLatitude(($extrema["minLat"] + $extrema["maxLat"]) / 2.0);
			$this->mReq->setLongitude(($extrema["minLong"] + $extrema["maxLong"]) / 2.0);
			$this->mReq->setBackground($inReq->getBackground());

			// compute lat and long ranges, with a lower bound in case there's only one location in the set
		    $longRange = abs($extrema["maxLong"] - $extrema["minLong"]);
		    $latRange = abs($extrema["maxLat"] - $extrema["minLat"]);

			// using the aspect ratio, decide on how to scale the map so it fits all the points
		    $minRange = min($latRange, $longRange * $this->mReq->getMapheight() / $this->mReq->getMapWidth());
		    if ($minRange == 0) $minRange = 1;
			$this->mReq->setScale($minRange);
		}
	}

	function getPageURL() { return $this->mPageURL; }
	function getMinimumLatitude() { return $this->mReq->getLatitude() - $this->getLatitudeRadius(); }
	function getMaximumLatitude() { return $this->mReq->getLatitude() + $this->getLatitudeRadius(); }
	function getMinimumLongitude() { return $this->mReq->getLongitude() - $this->getLongitudeRadius(); }
	function getMaximumLongitude() { return $this->mReq->getLongitude() + $this->getLongitudeRadius(); }
	function getLatitudeRadius() { return $this->mReq->getScale(); }
	function getLongitudeRadius() { return $this->mReq->getScale() * ($this->mReq->getMapWidth() / $this->mReq->getMapHeight()); }

	function scaleLat($inLat)
	{
		return $this->mReq->getMapHeight() - $this->mReq->getMapHeight() * ($inLat - $this->getMinimumLatitude()) /
			($this->getMaximumLatitude() - $this->getMinimumLatitude());
	}

	function scaleLong($inLong)
	{
		return $this->mReq->getMapWidth() * ($inLong - $this->getMinimumLongitude()) /
			($this->getMaximumLongitude() - $this->getMinimumLongitude());
	}

	function performDBQuery()
	{
		return performQuery("Find map items",
							$this->mLocationQuery->getSelectClause() . ", location.Latitude, location.Longitude" .
							$this->mLocationQuery->getFromClause() . " " . 
							$this->mLocationQuery->getWhereClause() .
							" GROUP BY location.objectid ORDER BY location.Latitude desc, location.Longitude");
	}

	function getBackgroundImageURL()
	{
		// many layers of labels and boundaries
		$roads =
			"http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap?" . 
			"WMTVER=1.1.0&servicename=USGS_WMS_REF&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
			$this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			"&WIDTH=" . $this->mReq->getMapWidth() . "&HEIGHT=" . $this->mReq->getMapHeight() .
			"&LAYERS=States,State_Labels,County_Labels,County,Route_Numbers,Roads,Streams,Names-Streams,Water_Bodies,Names-Water_Bodies,Urban_Areas,Federal_Lands,Names-Federal_Lands&STYLES=reference&FORMAT=JPEG&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		// nice shaded color relief but low res
		$relief2 =
			 "http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap/USGS_WMS_GTOPO?" . 
			 "WMTVER=1.0.0&LAYERS=GTOPO60%20Color%20Shaded%20Relief&FORMAT=jpeg&REQUEST=GetMap&SRS=EPSG:4326&servicename=WMS&EXCEPTIONS=INIMAGE&BBOX=" . 
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&WIDTH=" . $this->mReq->getMapWidth() . "&HEIGHT=" . $this->mReq->getMapHeight();

		// higher res elevation but greyscale only
		$relief =
			 "http://gisdata.usgs.net/servlet/com.esri.wms.Esrimap?" .
			 "WMTVER=1.0.0&servicename=USGS_WMS_NED&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&WIDTH=" . $this->mReq->getMapWidth() . "&HEIGHT=" . $this->mReq->getMapHeight() .
			 "&LAYERS=US_NED_Shaded_Relief&STYLES=reference&FORMAT=JPEG&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		// different kinds of land cover (needs legend)
		$landcover =
			 "http://ims.cr.usgs.gov/servlet/com.esri.wms.Esrimap?" . 
			 "WMTVER=1.0.0&LAYERS=US_NLCD&FORMAT=PNG&BGCOLOR=0x000000&TRANSPARENT=true&SRS=EPSG:4326&SERVICE=WMS&STYLES=&SERVICENAME=USGS_EDC_LandCover_NLCD&BBOX=" . 
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&REQUEST=map" . 
			 "&WIDTH=" . $this->mReq->getMapWidth() . "&HEIGHT=" . $this->mReq->getMapHeight() .
			 "&LAYERS=US_NED_Shaded_Relief&STYLES=reference&FORMAT=JPEG&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

		// medium res landsat
		$landsat = 
			 "http://ims.cr.usgs.gov:80/servlet/com.esri.wms.Esrimap/USGS_WMS_LANDSAT7?" . 
			 "WMTVER=1.0.0&servicename=WMS&reaspect=True&REQUEST=map&SRS=EPSG:4326&BBOX=" . 
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&WIDTH=" . $this->mReq->getMapWidth() . "&HEIGHT=" . $this->mReq->getMapHeight() .
			 "&LAYERS=LANDSAT7&STYLES=reference&FORMAT=JPEG&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";


		// seemless high res b/w photo
		$terraserver =
			 "http://terraserver.microsoft.com/ogcmap.ashx?" .
			 "version=1.1.1&request=GetMap&Layers=DOQ&Styles=GeoGrid&SRS=EPSG:4326&BBOX=" .
			 $this->getMinimumLongitude() . "," . $this->getMinimumLatitude(). "," . $this->getMaximumLongitude() . "," . $this->getMaximumLatitude() .
			 "&WIDTH=" . $this->mReq->getMapWidth() . "&HEIGHT=" . $this->mReq->getMapHeight() .
			 "&format=image/jpeg&Exceptions=se_xml";

		if ($this->mReq->getBackground() == "roads")
		{
			return $roads;
		}
		else if ($this->mReq->getBackground() == "landcover")
		{
			return $landcover;
		}
		else if ($this->mReq->getBackground() == "photo")
		{
			if ($this->mReq->getScale() > 0.02)
			{
				return $landsat;
			}
			else
			{
				return $terraserver;
			}
		}
		else //if ($this->mReq->getBackground() == "relief")
		{
			if ($this->mReq->getScale() > 1.3)
			{
				return $relief2;
			}
			else
			{
				return $relief;
			}
		}
	}

	function linkToSelfChangeBackground($background)
	{
		$oldBackground = $this->mReq->getBackground();
		$this->mReq->setBackground($background);
		$link = $this->mReq->linkToSelf($background);
		$this->mReq->setBackground($oldBackground);
		return $link;
	}

	function linkToSelfZoom($zoomFactor, $anchorText)
	{
		$oldScale = $this->mReq->getScale();
		$this->mReq->setScale($zoomFactor * $oldScale);
		$link = $this->mReq->linkToSelf($anchorText);
		$this->mReq->setScale($oldScale);
		return $link;
	}

	function linkToSelfPan($latPan, $longPan, $anchorText)
	{
		$oldLat = $this->mReq->getLatitude();
		$oldLong = $this->mReq->getLongitude();

		$this->mReq->setLongitude($oldLong + $longPan);
		$this->mReq->setLatitude($oldLat + $latPan);

		$link = $this->mReq->linkToSelf($anchorText);

		$this->mReq->setLatitude($oldLat);
		$this->mReq->setLongitude($oldLong);

		return $link;
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
	<div style="z-index: 900; text-align: center; width: 20px; height: 20px; border: 1px solid gray; font-weight: bold; background-color: white; position: absolute; left: 26px; top: 5px">
		 <?= $this->linkToSelfPan($latPan, 0, "N"); ?>
	</div>
	<div style="z-index: 900; text-align: center; width: 20px; height: 20px; border: 1px solid gray; font-weight: bold; background-color: white; position: absolute; left: 26px; top: 47px">
		 <?= $this->linkToSelfPan(-$latPan, 0, "S"); ?>
	</div>
	<div style="z-index: 900; text-align: center; width: 20px; height: 20px; border: 1px solid gray; font-weight: bold; background-color: white; position: absolute; left: 5px; top: 26px">
		 <?= $this->linkToSelfPan(0, $longPan, "W"); ?>
	</div>
	<div style="z-index: 900; text-align: center; width: 20px; height: 20px; border: 1px solid gray; font-weight: bold; background-color: white; position: absolute; left: 47px; top: 26px">
		 <?= $this->linkToSelfPan(0, -$longPan, "E"); ?>
	</div>
<?
	}


	function draw($inDrawControls = false)
	{
		$centerLong = ($this->getMinimumLongitude() + $this->getMaximumLongitude()) / 2.0;
		$centerLat = ($this->getMinimumLatitude() + $this->getMaximumLatitude()) / 2.0;

		$minRange = min($this->getMaximumLongitude() - $this->getMinimumLongitude(), $this->getMaximumLatitude() - $this->getMinimumLatitude());
		$minPixels = min($this->mReq->getMapWidth(), $this->mReq->getMapHeight());

		$longRange = $minRange * $this->mReq->getMapHeight() / $minPixels;
		$latRange = $minRange * $this->mReq->getMapWidth() / $minPixels;

		$longPan = $longRange * 0.1;
		$latPan = $latRange * 0.3; 
?>

	   <? if ($inDrawControls) { ?>
	      <div style="text-align: right; padding-top: 30px;">
		    <?= $this->linkToSelfZoom(1.5, "out") ?> | 
			<?= $this->linkToSelfZoom(0.6, "in") ?> |
			<?= $this->drawLayerControls() ?>
          </div>
	   <? } ?>

       <div style="position: relative; border: 1px solid gray; height:<?= $this->mReq->getMapHeight() ?>px; width: <?= $this->mReq->getMapWidth()?>px;">

	   <img id="theMap" src="./images/loading.gif" width="<?= $this->mReq->getMapWidth()?>px" height="<?= $this->mReq->getMapHeight() ?>px"/>

       <script language = javascript>
          document.getElementById('theMap').src="<?= $this->getBackgroundImageURL(); ?>"
       </script>

<?
		if ($inDrawControls) $this->drawPanControls();

		$dbQuery = $this->performDBQuery();

		$counter = 1;

		while($info = mysql_fetch_array($dbQuery))
		{
			$margin = 5;
			$lat = $info["Latitude"];
			$top = round($this->scaleLat($lat)) - 8;
			
			$long = $info["Longitude"];
			$left = round($this->scaleLong($long)) - 8; 

			// TODO, check for points out of range
			
			if (($left > $margin) && ($left < $this->mReq->getMapWidth() - $margin) &&
				($top > $margin) && ($top < $this->mReq->getMapHeight() - $margin))
			{
				$locationInfo[$counter] = $info;

				$clickRequest = new Request;
				$clickRequest->setLatitude($info["Latitude"]);
				$clickRequest->setLongitude($info["Longitude"]);
				$clickRequest->setScale(0.5 * $this->mReq->getScale());
 ?>
			    <div style="position: absolute; left: <?= $left ?>px; top: <?= $top ?>px" nowrap>
				  <?=  $clickRequest->linkToSelf("<img border=\"0\" width=\"15\"  src=\"./images/mapspot.gif\"/><span>" . $info["Name"] . "</span>", "info") ?>
			    </div>
<?
			    $counter++;
			}
		}
?>

   </div>
  
   <p>&nbsp;</p>

<?
     if ($inDrawControls) {
	   $this->mLocationQuery->formatTwoColumnLocationList("map", true);
     }
   }
}

?>