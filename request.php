<?php

require_once("./speciesquery.php");
require_once("./locationquery.php");
require_once("./tripquery.php");
require_once("./map.php");
require_once("./chronolist.php");

class Request
{
	//
	// INSTANCE VARIABLES
	//

	var $mView; // what viewing mode for this page
	var $mPageScript; // which PHP page script is this

	var $mSightingID; // which sighting is this page about

	var $mLocationID; // constrain this query to a particular location
	var $mCounty; // constrain this query to a particular county
	var $mStateID; // constrain this query to a particular state

	var $mTripID; // constrain this query to a trip
	var $mMonth; // constrain this query to a particular month
	var $mYear; // constrain this query to a particular year

	var $mSpeciesID; // constrain this query to a particular species	
	var $mFamilyID; // constrain this query to a particular family
	var $mOrderID; // constrain this query to a particular order
	
	var $mMapHeight; // map height
	var $mMapWidth; // map width
	var $mLatitude; // latitude of the center of the map
	var $mLongitude; // longitude of the center of the map
	var $mScale; // map scale
	var $mBackground; // which map background

	//
	// CONSTRUCTOR
	//

	function Request()
	{
		array_key_exists("view", $_GET) && $this->setView($_GET["view"]);
		$this->setPageScript(array_pop(explode("/", $_SERVER["SCRIPT_FILENAME"])));

		array_key_exists("sightingid", $_GET) && $this->setSightingID($_GET["sightingid"]);
		
		array_key_exists("stateid", $_GET) && $this->setStateID($_GET["stateid"]);
		array_key_exists("county", $_GET) && $this->setCounty($_GET["county"]);
		array_key_exists("locationid", $_GET) && $this->setLocationID($_GET["locationid"]);

		array_key_exists("year", $_GET) && $this->setYear($_GET["year"]);
		array_key_exists("month", $_GET) && $this->setMonth($_GET["month"]);
		array_key_exists("tripid", $_GET) && $this->setTripID($_GET["tripid"]);

		array_key_exists("orderid", $_GET) && $this->setOrderID($_GET["orderid"]);
		array_key_exists("familyid", $_GET) && $this->setFamilyID($_GET["familyid"]);
		array_key_exists("speciesid", $_GET) && $this->setSpeciesID($_GET["speciesid"]);

		$this->setMapHeight(320); array_key_exists("height", $_GET) && $this->setMapHeight($_GET["height"]);
		$this->setMapWidth(640); array_key_exists("width", $_GET) && $this->setMapWidth($_GET["width"]);
		array_key_exists("lat", $_GET) && $this->setLatitude($_GET["lat"]);
		array_key_exists("long", $_GET) && $this->setLongitude($_GET["long"]);
		array_key_exists("scale", $_GET) && $this->setScale($_GET["scale"]);
		array_key_exists("backgnd", $_GET) && $this->setBackground($_GET["backgnd"]);
	}

	//
	// GETTERS AND SETTERS
	//


	// PAGE MANAGEMENT

	function setView($inValue) { $this->mView = $inValue; }
	function getView() { return $this->mView; }
	function setPageScript($inValue) { $this->mPageScript = $inValue; }
	function getPageScript() { return $this->mPageScript; }

	// SIGHTINGS

	function setSightingID($inValue) { $this->mSightingID = $inValue; }
	function getSightingID() { return $this->mSightingID; }

	// GEOGRAPHY

	function setLocationID($inValue)
	{
		$this->mLocationID = $inValue;

		if ($inValue != "")
		{
			$locationInfo = $this->getLocationInfo();
			$stateInfo = getStateInfoForAbbreviation($locationInfo["State"]);
			$this->setStateID($stateInfo["objectid"]);
			$this->setCounty($locationInfo["County"]);
		}
	}
	function getLocationID() { return $this->mLocationID; }
	function setCounty($inValue) { $this->mCounty = $inValue; }
	function getCounty() { return $this->mCounty; }
	function setStateID($inValue) { $this->mStateID = $inValue; }
	function getStateID() { return $this->mStateID; }

	// CHRONOLOGY

	function setTripID($inValue)
	{
		$this->mTripID = $inValue;

		if ($inValue != "")
		{
			$tripInfo = $this->getTripInfo();
			$this->setYear(substr($tripInfo["Date"], 0, 4));
			$this->setMonth(substr($tripInfo["Date"], 5, 2));
		}
	}
	function getTripID() { return $this->mTripID; }
	function setMonth($inValue) { $this->mMonth = $inValue; }
	function getMonth() { return $this->mMonth; }
	function setYear($inValue) { $this->mYear = $inValue; }
	function getYear() { return $this->mYear; }

	// TAXONOMY

    function setSpeciesID($inValue)
	{
		$this->mSpeciesID = $inValue;
		if ($inValue != "") { $this->setFamilyID(floor($inValue / 10000000)); }
	}
    function getSpeciesID() { return $this->mSpeciesID; }
	function setFamilyID($inValue)
	{
		$this->mFamilyID = $inValue;
		if ($inValue != "") { $this->setOrderID(floor($inValue / 100)); }
	}
	function getFamilyID() { return $this->mFamilyID;  }
	function setOrderID($inValue) { $this->mOrderID = $inValue; }
	function getOrderID() { return $this->mOrderID; }

	// MAPS

	function setMapHeight($inValue) { $this->mMapHeight = $inValue; }
	function getMapHeight() { return $this->mMapHeight; }
	function setMapWidth($inValue) { $this->mMapWidth = $inValue; }
	function getMapWidth() { return $this->mMapWidth; }
	function setLatitude($inValue) { $this->mLatitude = $inValue; }
	function getLatitude() { return $this->mLatitude; }
	function setLongitude($inValue) { $this->mLongitude = $inValue; }
	function getLongitude() { return $this->mLongitude; }
	function setScale($inValue) { $this->mScale = $inValue; }
	function getScale() { return $this->mScale; }
	function setBackground($inValue) { $this->mBackground = $inValue; }
	function getBackground() { return $this->mBackground; }

	function getParams()
	{
		$params = NULL;

		if ($this->mView != "") { $params[] = "view=" . $this->mView; }

		if ($this->mLocationID != "") { $params[] = "locationid=" . $this->mLocationID; }
		if ($this->mCounty != "") { $params[] = "county=" . $this->mCounty; }
		if ($this->mStateID != "") { $params[] = "stateid=" . $this->mStateID; }

		if ($this->mTripID != "") { $params[] = "tripid=" . $this->mTripID; }

		if ($this->mSpeciesID != "") { $params[] = "speciesid=" . $this->mSpeciesID; }
		if ($this->mFamilyID != "") { $params[] = "familyid=" . $this->mFamilyID; } 
		if ($this->mOrderID != "") { $params[] = "orderid=" . $this->mOrderID; }
		
		if ($this->mMonth !="") { $params[] = "month=" . $this->mMonth; }
		if ($this->mYear !="") { $params[] = "year=" . $this->mYear; }

		if ($this->mLatitude !="") { $params[] = "lat=" . $this->mLatitude; }
		if ($this->mLongitude !="") { $params[] = "long=" . $this->mLongitude; }

		if ($this->mScale !="") { $params[] = "scale=" . $this->mScale; }
		if ($this->mBackground !="") { $params[] = "backgnd=" . $this->mBackground; }

		if (count($params) == 0)
		{
			return "";
		}
		else 
		{
			return implode("&", $params);
		}
	}

	function debug()
	{
		echo "\n<!-- view " . $this->mView . " locationid " . $this->mLocationID . " county " . $this->mCounty .
			" stateid " . $this->mStateID . " tripid " . $this->mTripID .
			" month " . $this->mMonth . " year " . $this->mYear . 
			" speciesid " . $this->mSpeciesID . " family " . $this->mFamilyID . " order " . $this->mOrderID . " -->\n\n";
	}

	function command($linkText)
	{
		if (strlen($linkText) > 18)
		{
			return $this->linktoSelf(strtolower(substr($linkText, 0, 18) . "..."), "commandlink");
		}
		else
		{
			return $this->linkToSelf(strtolower($linkText), "commandlink");
		}
	}

	function linkToSelf($linkText, $class = "")
	{
		if ($class == "")
		{
			return "<a href=\"./" . $this->getPageScript() . "?" . $this->getParams() . "\">" . $linkText . "</a>";
		}
		else
		{
			return "<a class=\"" . $class . "\" href=\"./" . $this->getPageScript() . "?" . $this->getParams() . "\">" . $linkText . "</a>";
		}
	}

	function linkToSlideshow()
	{
		// TODO: this can cause redundant queries by calling setLocationID...
		$newRequest = new Request;

		$newRequest->setView("");
		$newRequest->setLatitude("");
		$newRequest->setLongitude("");
		$newRequest->setScale("");
?>
<SCRIPT LANGUAGE="JavaScript">
<!--

// Copyright (c) 2000 internet.com Corp.
// http://www.webreference.com/js/
// License is granted if and only if this entire
// copyright notice is included. By Tomer Shiran.

function launch(newURL, newName, newFeatures, orgName) {
  var remote = open(newURL, newName, newFeatures);
  if (remote.opener == null)
    remote.opener = window;
  remote.opener.name = orgName;
  return remote;
}

function launchSlideshow() {
  myRemote = launch("./slideshow.php?<?= $this->getParams() ?>",
                    "myRemote",
                    "height=600,width=650,alwaysLowered=0,alwaysRaised=0,channelmode=0,dependent=0,directories=0,fullscreen=0,hotkeys=1,location=0,menubar=0,resizable=0,scrollbars=0,status=0,titlebar=1,toolbar=0,z-lock=0",
                    "myWindow");
}

// -->
</SCRIPT>
<a  href="javascript:void(0)" onclick="launchSlideshow()">slideshow</a>
<?
	}

	function linkToSelfChangeView($view, $linkText)
	{
		// TODO: this can cause redundant queries by calling setLocationID...
		$newRequest = new Request;

		$newRequest->setView($view);
		$newRequest->setLatitude("");
		$newRequest->setLongitude("");
		$newRequest->setScale("");

		$link = $newRequest->linkToSelf($linkText);

		return $link;
	}

	// if this request were further bound by time and location, should it display a list of species, or a list of sightings?
	function getTimeAndLocationScript()
	{
		if ($this->getSpeciesID() == "")
		{
			return "specieslist.php";
		}
		else
		{
			return "sightinglist.php";
		}
	}

	function getSpeciesInfo()
	{
		if ($this->getSpeciesID() == '') die("Fatal error: missing SpeciesID");
		return getSpeciesInfo($this->getSpeciesID());
	}

	function getOrderInfo()
	{
		if ($this->getOrderID() == '') die("Fatal error: missing Order ID");
		return getOrderInfo($this->getOrderID() * 1000000000);
	}

	function getLocationInfo()
	{
		if ($this->getLocationID() == '') die("Fatal error: missing Location ID");
		return getLocationInfo($this->getLocationID());
	}

	function getFamilyInfo()
	{
		if ($this->getFamilyID() == '') die("Fatal error: missing Family ID");
		return getFamilyInfo($this->getFamilyID() * 10000000);
	}

	function getTripInfo()
	{
		if ($this->getTripID() == '') die("Fatal error: missing Trip ID");
		return getTripInfo($this->getTripID());
	}

	function getStateInfo()
	{
		if ($this->getStateID() == '') die("Fatal error: missing State ID");
		return getStateInfo($this->getStateID());
	}

	function getSightingInfo()
	{
		if ($this->getSightingID() == '') die("Fatal error: missing Sighting ID");
		return getSightingInfo($this->getSightingID());
	}

	function handleStandardViews()
	{
		if ($this->getView() == 'trips')
		{
			$tripQuery = new TripQuery($this);
			doubleCountHeading($tripQuery->getTripCount(), "trip", $tripQuery->getPhotoCount(), "with photo");
			$tripQuery->formatTwoColumnTripList();
		}
		elseif ($this->getView() == 'tripsummaries')
		{
			$tripQuery = new TripQuery($this);
			countHeading( $tripQuery->getTripCount(), "trip");
			$tripQuery->formatSummaries();
		}
		elseif ($this->getView() == 'species')
		{
			$speciesQuery = new SpeciesQuery($this);
			doubleCountHeading($speciesQuery->getSpeciesCount(), "species", $speciesQuery->getPhotoCount(), "with photo");
			$speciesQuery->formatTwoColumnSpeciesList(); 
		}
		elseif ($this->getView() == 'speciesbyyear')
		{
			$speciesQuery = new SpeciesQuery($this);
			countHeading( $speciesQuery->getSpeciesCount(), "species");
			$speciesQuery->formatSpeciesByYearTable(); 
		}
		elseif ($this->getView() == 'speciesbymonth')
		{
			$speciesQuery = new SpeciesQuery($this);
			countHeading( $speciesQuery->getSpeciesCount(), "species");
			$speciesQuery->formatSpeciesByMonthTable(); 
		}
		elseif ($this->getView() == 'locations')
		{
			$locationQuery = new LocationQuery($this);
			doubleCountHeading( $locationQuery->getLocationCount(), "location", $locationQuery->getPhotoCount(), "with photo");
			$locationQuery->formatTwoColumnLocationList($this->getView(), false);
		}
		elseif ($this->getView() == 'locationsbyyear')
		{
			$locationQuery = new LocationQuery($this);
			countHeading( $locationQuery->getLocationCount(), "location");
			$locationQuery->formatLocationByYearTable();
		}
		elseif ($this->getView() == 'locationsbymonth')
		{
			$locationQuery = new LocationQuery($this);
			countHeading( $locationQuery->getLocationCount(), "location");
			$locationQuery->formatLocationByMonthTable();
		}
		else if ($this->getView() == "map")
		{
			$map = new Map("./" . $this->getPageScript(), $this);
			$map->draw(true);
		}
		else if ($this->getView() == "chrono")
		{
			$chrono = new ChronoList($this);
			$chrono->draw();
		}
		elseif ($this->getView() == 'photo')
		{
			$sightingQuery = new SightingQuery($this);
			$sightingQuery->formatPhotos();
		}
		else
		{
			die("Fatal error: Unknown view mode '" . $this->getView() . "'");
		}
	}


	function optionSelectedViewHelper($inName, $inValue)
	{
		echo "\n<option value=\"" . $inValue . "\"";
		if ($this->getView() == $inValue) echo " selected";
		echo ">" . $inName . "</option>";
	}
	
	function viewLinks($inDefaultView)
	{ 
		if ($this->getView() == "") { $this->setView($inDefaultView); } 

		$tempRequest = new Request;
		$tempRequest->setView("");	 
?>


<SCRIPT LANGUAGE="JavaScript">
<!--
function changeView()
{
	box = document.forms[0].viewChooser;
	destination = "./<?= $tempRequest->getPageScript() ?>?<?= $tempRequest->getParams() ?>&view=" + box.options[box.selectedIndex].value;
	if (destination) location.href = destination;
}
// -->
</SCRIPT>

	    <div class="viewlinks">
	      <form class="viewlinks">
	        view: <select name="viewChooser" onChange="changeView()"> <?

		if ($this->getTripID() == "")
		{ ?>
		  <optgroup label="trips">
<?		    $this->optionSelectedViewHelper("list", "trips"); ?>
		  </optgroup>
<?		} ?>

		  <optgroup label="locations"><?

		  if ($this->getLocationID() == "")
		  {
			  if ($this->getTripID() == "" )
			  {
				  $this->optionSelectedViewHelper("list", "locations");
			  }
			  
			  if ($this->getMonth() == "" )
			  {
				  $this->optionSelectedViewHelper("by month", "locationsbymonth");
				  
				  if ($this->getYear() == "" )
				  {
					  $this->optionSelectedViewHelper("by year", "locationsbyyear");
				  }
			  }
		  }

	      $this->optionSelectedViewHelper("map", "map"); ?>

		  </optgroup> <?
		
		if ($this->getSpeciesID() == "")
		{ ?>
			<optgroup label="species"> <?
		    $this->optionSelectedViewHelper("by taxonomy", "species");
		    if ($this->getTripID() == "" )
			{
				$this->optionSelectedViewHelper("by date", "chrono");
			}

			if ($this->getMonth() == "" )
			{
				$this->optionSelectedViewHelper("by month", "speciesbymonth");

				if ($this->getYear() == "" )
				{
					$this->optionSelectedViewHelper("by year", "speciesbyyear");
				}
			} ?>
			</optgroup><?
		}  ?>

		<optgroup label="photos"> <?
		   $this->optionSelectedViewHelper("thumbnails", "photo"); ?>
		</optgroup>

		?></select> <? $this->linkToSlideshow(); ?></form><?
	}

	function globalMenuBirds()
	{
		if (! strstr(getenv("SCRIPT_NAME"), "species") &&
			$this->getOrderID() == "" && $this->getFamilyID() == "" && $this->getSpeciesID() == "")
		{ ?>
			<div class="command-disabled"><a href="./speciesindex.php">birds</a></div<?
		}
		else
		{
?>			<div class="command"><a class="commandlink" href="./speciesindex.php">birds</a>
<?
			if ($this->getOrderID() != "")
			{
				$orderInfo = $this->getOrderInfo();
				$orderRequest = new Request;
				$orderRequest->setPageScript("orderdetail.php");
				$orderRequest->setSpeciesID("");
				$orderRequest->setFamilyID(""); ?>
			
				<div class="subcommand1"><?= $orderRequest->command($orderInfo["LatinName"]) ?></div>
<?		    }
		
			if ($this->getFamilyID() != "")
			{
				$familyInfo = $this->getFamilyInfo();
				
				$familyRequest = new Request;
				$familyRequest->setPageScript("familydetail.php");
				$familyRequest->setSpeciesID(""); ?>
					 
				<div class="subcommand2"><?= $familyRequest->command($familyInfo["LatinName"]) ?></div>
<?		    }

			if ($this->getSpeciesID() != "")
			{
				$speciesInfo = $this->getSpeciesInfo();
				
				$speciesRequest = new Request;
				$speciesRequest->setPageScript("speciesdetail.php"); ?>
			
				 <div class="subcommand3"><?= $speciesRequest->command($speciesInfo["CommonName"]) ?></div>
<?		    }
?>          </div><?
		}
	}


	function globalMenuLocations()
	{
		if (! strstr(getenv("SCRIPT_NAME"), "location") &&
			$this->getStateID() == "" && $this->getCounty() == "" && $this->getLocationID() == "")
		{ ?>
			<div class="command-disabled"><a href="./locationindex.php">locations</a></div><?
		}
		else
		{
?>			<div class="command"><a class="commandlink" href="./locationindex.php">locations</a>
<?
			if ($this->getStateID() != "")
			{
				$stateInfo = $this->getStateInfo();
				$stateRequest = new Request;
				$stateRequest->setPageScript("statedetail.php");
				$stateRequest->setLocationID("");
				$stateRequest->setCounty(""); ?>
			
				<div class="subcommand1"><?= $stateRequest->command($stateInfo["Name"]) ?></div>
<?		    }
		
			if ($this->getCounty() != "")
			{
				$countyRequest = new Request;
				$countyRequest->setPageScript("countydetail.php");
				$countyRequest->setLocationID(""); ?>
					 
				<div class="subcommand2"><?= $countyRequest->command($this->getCounty() . " county") ?></div>
<?		    }

			if ($this->getLocationID() != "")
			{
				$locationInfo = $this->getLocationInfo();
				$locationRequest = new Request;
				$locationRequest->setPageScript("locationdetail.php"); ?>
			
				<div class="subcommand3"><?= $locationRequest->command($locationInfo["Name"]) ?></div>
<?		    }
?>          </div><?
		}
	}

	function globalMenuTrips()
	{
		if (! strstr(getenv("SCRIPT_NAME"), "trip") &&
			$this->getYear() == "" && $this->getMonth() == "" && $this->getTripID() == "")
		{ ?>
			<div class="command-disabled"><a href="./tripindex.php">trips</a></div><?
		}
		else
		{
?>			<div class="command">
				<a class="commandlink" href="./tripindex.php">trips</a>
<?
			if ($this->getYear() != "")
			{
				$yearRequest = new Request;
				$yearRequest->setPageScript("yeardetail.php");
				$yearRequest->setTripID("");
				$yearRequest->setMonth(""); ?>
			
				<div class="subcommand1"><?= $yearRequest->command($this->getYear()) ?></div>
<?		    }
		
			if ($this->getMonth() != "")
			{
				$monthRequest = new Request;
				$monthRequest->setPageScript("monthdetail.php");
				$monthRequest->setTripID(""); ?>
					 
				<div class="subcommand2"><?= $monthRequest->command(getMonthNameForNumber($this->getMonth())) ?></div>
<?		    }

			if ($this->getTripID() != "")
			{
				$tripInfo = $this->getTripInfo();
				$dayOfMonth = date("jS", strtotime($tripInfo["Date"]));
				$tripRequest = new Request;
				$tripRequest->setPageScript("tripdetail.php"); ?>
			
				<div class="subcommand3"<?= $tripRequest->command($dayOfMonth) ?></div>
<?		    }
?>          </div><?
		}
	}

	function globalMenu()
	{ ?>
		<div class="topleft">
		    <a href="./index.php">
		        <img src="./images/logo.gif" border=0 width="50px" alt="birdWalker logo"><br/>
		        <div>Birding field notes and photos from Bill Walker</div>
		    </a>
		</div>

		<div class="contentleft">
			
<?          $this->globalMenuTrips(); ?>
<?          $this->globalMenuBirds(); ?>
<?          $this->globalMenuLocations(); ?>

			<div class="command-disabled"><a href="./speciesindex.php?view=chrono">life list</a></div>
	        <div class="command-disabled"><a href="./credits.php">about</a></div>

<?	        if (getEnableEdit())
	        { ?>
	        <div class="command-disabled"><a href="./tripcreate.php">create trip</a></div>
            <div class="command-disabled"><a href="./photosneeded.php">photo todo</a></div>
	        <div class="command-disabled"><a href="./errorcheck.php">db todo</a></div>
<?	        } ?>

	        <div class="command-disabled"><a href="./indexrss.php">RSS</a></div>

        </div>
<?  }

}