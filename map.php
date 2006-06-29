
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

	function getPageURL() { return $this->mPageURL; }

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
      $extrema = $this->mLocationQuery->findExtrema();
      
      $mMinLat = $extrema["minLat"];
      $mMaxLat = $extrema["maxLat"];
      $mMinLong = $extrema["minLong"];
      $mMaxLong = $extrema["maxLong"];
      
      // put the map in the center of the extrema
      $this->mReq->setLatitude(($mMinLat + $mMaxLat) / 2.0);
      $this->mReq->setLongitude(($mMinLong + $mMaxLong) / 2.0);
?>

<html>
  <head>
    <script src="http://maps.google.com/maps?file=api&v=2&key=ABQIAAAAHB2OV0S5_ezvt-IsSEgTohRpTu2oewaAZF3JMvnmpq8AtvOtbRRR9bUrae9RcGIgtWO2REdkBQNLwA" type="text/javascript"></script>
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
var swCorner = new GLatLng(<?= $mMinLat ?>, <?= $mMinLong ?>);
var neCorner = new GLatLng(<?= $mMaxLat ?>, <?= $mMaxLong ?>);
var map = new GMap2(document.getElementById("map"));
map.addControl(new GSmallMapControl());
<? if ($this->mReq->getMapWidth() > 300) { ?> map.addControl(new GMapTypeControl()); <? } ?>
map.setCenter(new GLatLng(<?= $this->mReq->getLatitude() ?>, <?= $this->mReq->getLongitude() ?>));
var bestZoom = map.getBoundsZoomLevel(new GLatLngBounds(swCorner, neCorner));
map.setZoom(bestZoom);

// Creates one of our tiny markers at the given point
function createMarker(point,infoHTML) {
  var marker = new GMarker(point, icon);
  map.addOverlay(marker);
  GEvent.addListener(marker, "click", function() {
    marker.openInfoWindowHtml(infoHTML);
  });
}

// Place the icons where they should be

<?
    $dbQuery = $this->performDBQuery();
  
    while($info = mysql_fetch_array($dbQuery))
    {
        if (($info["Longitude"] != 0) && ($info["Latitude"] != 0))
	{ ?>
           createMarker(new GPoint(<?= $info["Longitude"] ?>, <?= $info["Latitude"] ?>), "\
               <div class=\"report-content\">\
	           <a href=\"./locationdetail.php?locationid=<?= $info["objectid"] ?>\" target=\"_top\"><?= $info["Name"] ?></a>\
<?                 if ($info["locationPhotos"] > 0) { ?>\
                       <a href=\"./locationdetail.php?view=photo&locationid=<?= $info["objectid"] ?>\" target=\"_top\">\
                           <img border=\"0\" align=\"bottom\" src=\"./images/camera.gif\" alt=\"photo\">\
                       </a>\
<?                 } ?>\
               </div>");

<?      }
    }?>
    
//]]>
</script>
<p>
	
</body>
</html>	

<?
	  }
}

?>