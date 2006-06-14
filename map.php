
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
			"&LAYERS=roads&STYLES=reference&FORMAT=JPEG&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=INIMAGE";

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

	function draw($inDrawControls = false)
	{
	    $this->mReq
?>

       <iframe marginwidth="0" marginheight="0" scrolling="no" src="./mapframe.php?<?= $this->mReq->getParams() ?>" style="position: relative; border: 1px solid gray; height:<?= $this->mReq->getMapHeight() ?>px; width: <?= $this->mReq->getMapWidth()?>px;">
       </iframe>

	   <p>&nbsp;</p>
<?

	   if ($inDrawControls) $this->mLocationQuery->formatTwoColumnLocationList("map", true);
   }


	function drawInner()
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

       <div style="position: relative; border: 1px solid gray; height:<?= $this->mReq->getMapHeight() ?>px; width: <?= $this->mReq->getMapWidth()?>px;">

	   <img id="theMap" src="./images/loading.gif" width="<?= $this->mReq->getMapWidth()?>px" height="<?= $this->mReq->getMapHeight() ?>px"/>

       <script language = javascript>
          document.getElementById('theMap').src="<?= $this->getBackgroundImageURL(); ?>"
       </script>

<?
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

<? }






    function drawGoogle()
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

<!-- try http://www.spflrc.org/~walker/googlemap.php?name=pants&lat=35.384&long=-118.115 -->

<html>
  <head>
    <script src="http://maps.google.com/maps?file=api&v=1&key=ABQIAAAAHB2OV0S5_ezvt-IsSEgTohRpTu2oewaAZF3JMvnmpq8AtvOtbRRR9bUrae9RcGIgtWO2REdkBQNLwA" type="text/javascript"></script>
  </head>
  <body>
  
  
  <style type="text/css">

h1	{
	margin: 0 0 0 0;
	padding: 0;
	font: bold 20px Arial, Helvetica, sans-serif;
	color: white;
	}
</style>

</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">


  
    <div id="map" style="width: <?= $this->mReq->getMapWidth() ?>; height: <?= $this->mReq->getMapHeight() ?>"></div>
    <script type="text/javascript">
    //<![CDATA[
   
// Create our "tiny" marker icon
var icon = new GIcon();
icon.image = "http://labs.google.com/ridefinder/images/mm_20_red.png";
icon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
icon.iconSize = new GSize(12, 20);
icon.shadowSize = new GSize(22, 20);
icon.iconAnchor = new GPoint(6, 20);
icon.infoWindowAnchor = new GPoint(5, 1);

// Center the map on the location
var map = new GMap(document.getElementById("map"));
map.addControl(new GSmallMapControl());
//map.addControl(new GMapTypeControl());
map.centerAndZoom(new GPoint(<?= $centerLong ?>, <?= $centerLat ?>), 12);

// Creates one of our tiny markers at the given point
function createMarker(point,name) {
  var marker = new GMarker(point, icon);
  map.addOverlay(marker);
  GEvent.addListener(marker, "click", function() {
    marker.openInfoWindowHtml(name);
  });
}

// Place the icons where they should be

<?
		$dbQuery = $this->performDBQuery();

		$counter = 1;

		while($info = mysql_fetch_array($dbQuery))
		{ ?>
createMarker(new GPoint(<?= $info["Longitude"] ?>, <?= $info["Latitude"] ?>), "<?= $info["Name"] ?>");
<?		} ?>


    
    //]]>
    </script>
	


<p>
	
</body>
</html>	







<?
	  }














}

?>