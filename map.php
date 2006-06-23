
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
	}

	function computeExtrema()
        {
                if ($this->mReq->getLatitude() == "0") // special case, show whole united states
		{
		    $this->mReq->setLatitude(38);
		    $this->mReq->setLongitude(-96);
		    $this->mReq->setScale(15);
		}
		else if ($this->mReq->getLatitude() == "") // get parameters from location query
		{
 			$extrema = $this->mLocationQuery->findExtrema();

			// put the map in the center of the extrema
			$this->mReq->setLatitude(($extrema["minLat"] + $extrema["maxLat"]) / 2.0);
			$this->mReq->setLongitude(($extrema["minLong"] + $extrema["maxLong"]) / 2.0);

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

	function performDBQuery()
	{
		return performQuery("Find map items",
							$this->mLocationQuery->getSelectClause() . ", location.Latitude, location.Longitude" .
							$this->mLocationQuery->getFromClause() . " " . 
							$this->mLocationQuery->getWhereClause() .
							" GROUP BY location.objectid ORDER BY location.Latitude desc, location.Longitude");
	}

	function draw($inDrawControls = false)
	{
?>

       <iframe marginwidth="0" marginheight="0" scrolling="no" src="./mapframe.php?<?= $this->mReq->getParams() ?>" style="position: relative; border: 1px solid gray; height:<?= $this->mReq->getMapHeight() ?>px; width: <?= $this->mReq->getMapWidth()?>px;">
       </iframe>

	   <p>&nbsp;</p>
<?

	   if ($inDrawControls) $this->mLocationQuery->formatTwoColumnLocationList("map", true);
        }


    function drawGoogle()
    {
      $this->computeExtrema();

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
<? if ($this->mReq->getMapWidth() > 300) { ?> map.addControl(new GMapTypeControl()); <? } ?>
map.centerAndZoom(new GPoint(<?= $centerLong ?>, <?= $centerLat ?>), 14);

// Creates one of our tiny markers at the given point
function createMarker(point,name) {
  var marker = new GMarker(point, icon);
  map.addOverlay(marker);
  GEvent.addListener(marker, "click", function() {
    marker.openInfoWindowHtml("<div class=\"report-content\">" + name + "</div>");
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