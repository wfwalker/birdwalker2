<?php

require_once("speciesquery.php");
require_once("locationquery.php");
require_once("tripquery.php");
require_once("map.php");
require_once("chronolist.php");

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
	var $mDayOfMonth; // constrain this query to a particular month
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

	var $mTripInfo; // cached trip info

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
		array_key_exists("dayofmonth", $_GET) && $this->setDayOfMonth($_GET["dayofmonth"]);
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

		$this->mTripInfo = "";
	}

	//
	// GETTERS AND SETTERS
	//

 	function isTripSpecified() { if ($this->getTripID() == '') return false; else return true; }
 	function isSpeciesSpecified() { if ($this->getSpeciesID() == "") return false; else return true; }
 	function isFamilySpecified() { if ($this->getFamilyID() == '') return false; else return true; }
 	function isOrderSpecified() { if ($this->getOrderID() == '') return false; else return true; }
	function isLocationSpecified() { if ($this->getLocationID() == '') return false; else return true; }
	function isCountySpecified() { if ($this->getCounty() == '') return false; else return true; }
	function isStateSpecified() { if ($this->getStateID() == '') return false; else return true; }
	function isDayOfMonthSpecified() { if ($this->getDayOfMonth() == '') return false; else return true; }
	function isMonthSpecified() { if ($this->getMonth() == '') return false; else return true; }
	function isYearSpecified() { if ($this->getYear() == '') return false; else return true; }

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
			$stateInfo = getStateInfoForAbbreviation($locationInfo["state"]);
			$this->setStateID($stateInfo["id"]);
			$this->setCounty($locationInfo["county"]);
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
		  // TODO this is causing extra queries when doing the lefthand global menu
		  echo "<!-- calling from inside setTrip ID-->";
			$tripInfo = $this->getTripInfo();
			$this->setYear(substr($tripInfo["date"], 0, 4));
			$this->setMonth(substr($tripInfo["date"], 5, 2));
		}
	}
	function getTripID() { return $this->mTripID; }
	function setDayOfMonth($inValue) { $this->mDayOfMonth = $inValue; }
	function getDayOfMonth() { return $this->mDayOfMonth; }
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
		
		if ($this->mDayOfMonth !="") { $params[] = "dayofmonth=" . $this->mDayOfMonth; }
		if ($this->mMonth !="") { $params[] = "month=" . $this->mMonth; }
		if ($this->mYear !="") { $params[] = "year=" . $this->mYear; }

		if ($this->mLatitude !="") { $params[] = "lat=" . $this->mLatitude; }
		if ($this->mLongitude !="") { $params[] = "long=" . $this->mLongitude; }

		if ($this->mScale !="") { $params[] = "scale=" . $this->mScale; }
		if ($this->mBackground !="") { $params[] = "backgnd=" . $this->mBackground; }

		if ($this->mMapWidth !="") { $params[] = "width=" . $this->mMapWidth; }
		if ($this->mMapHeight !="") { $params[] = "height=" . $this->mMapHeight; }

		if (count($params) == 0)
		{
			return "";
		}
		else 
		{
			return implode("&", $params);
		}
	}


	function getPageTitle($inPrefix = "")
	{
		$pageTitleItems = "";

		if ($inPrefix != "") {
			$pageTitleItems[] = $inPrefix;
		}

		if ($this->isSpeciesSpecified()) {
			$speciesInfo = $this->getSpeciesInfo();
			$pageTitleItems[] = $speciesInfo["common_name"];
		} elseif ($this->isFamilySpecified()) {
			$familyInfo = $this->getFamilyInfo();
			$pageTitleItems[] = $familyInfo["common_name"];
		} elseif ($this->isOrderSpecified()) {
			$orderInfo = $this->getOrderInfo();
			$pageTitleItems[] = $orderInfo["common_name"];
		}

		if ($this->isLocationSpecified()) {
			$locationInfo = $this->getLocationInfo();
			$pageTitleItems[] = $locationInfo["name"];
		} elseif ($this->isCountySpecified()) {
			$pageTitleItems[] = $this->getCounty() . " County";
		} elseif ($this->isStateSpecified()) {
			$stateInfo = $this->getStateInfo();
		    $pageTitleItems[] = $stateInfo["name"];
		}

		if ($this->isMonthSpecified()) {
		    if ($this->isDayOfMonthSpecified()) {
			    $pageTitleItems[] = getMonthNameForNumber($this->getMonth()) . " ". $this->getDayOfMonth();
			} else {
			    $pageTitleItems[] = getMonthNameForNumber($this->getMonth());
			}
		}
		if ($this->isYearSpecified()) {
			$pageTitleItems[] = $this->getYear();
		}

		if ($pageTitleItems == "")
		{
			return "";
		}
		else
		{
			return implode(", ", $pageTitleItems);
		}
	}

	function debug()
	{
		echo "\n<!-- view " . $this->mView . " locationid " . $this->mLocationID . " county " . $this->mCounty .
			" stateid " . $this->mStateID . " tripid " . $this->mTripID .
			" dayofmonth " . $this->mDayOfMonth . " month " . $this->mMonth . " year " . $this->mYear . 
			" speciesid " . $this->mSpeciesID . " family " . $this->mFamilyID . " order " . $this->mOrderID . " -->\n\n";
	}

	function command($linkText)
	{
		if (strlen($linkText) > 16)
		{
			return $this->linktoSelf(strtolower(substr($linkText, 0, 16) . "..."), "commandlink");
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
	    if ($this->mTripInfo == "")
		{
		    if ($this->getTripID() == '') die("Fatal error: missing Trip ID");
		    $this->mTripInfo = getTripInfo($this->getTripID());
		}
		return $this->mTripInfo;
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
			$tripQuery->formatTwoColumnTripList();
		}
		elseif ($this->getView() == 'species' || $this->getView() == 'speciesbylocation')
		{
		    // NOTE: the speciesbylocation case is implemented only in tripdetail.php
		    // it is only for single trip detail pages
			$speciesQuery = new SpeciesQuery($this);
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

	if (box.options[box.selectedIndex].value == "slideshow")
	{
	  destination = "./slideshow.php?<?= $tempRequest->getParams() ?>&origin=<?= $tempRequest->getPageScript() ?>";
		if (destination) location.href = destination;
	}
	else
	{
		destination = "./<?= $tempRequest->getPageScript() ?>?<?= $tempRequest->getParams() ?>&view=" + box.options[box.selectedIndex].value;
		if (destination) location.href = destination;
	}
}
// -->
</SCRIPT>

	    <div class="prevlink">
	      <form>
	        view: <select name="viewChooser" onChange="changeView()"> <?

		if ($this->getTripID() == "")
		{
		    $this->optionSelectedViewHelper("trip list", "trips");
		}

		if ($this->getLocationID() == "")
		{
		    $this->optionSelectedViewHelper("location list", "locations");
			  
			if ($this->getMonth() == "" )
			{
			    $this->optionSelectedViewHelper("locations by month", "locationsbymonth");
			}  
			if ($this->getYear() == "" )
			{
			    $this->optionSelectedViewHelper("locations by year", "locationsbyyear");
			}
		}

		$this->optionSelectedViewHelper("location map", "map");

		if ($this->getSpeciesID() == "")
		{
		    $this->optionSelectedViewHelper("species by taxonomy", "species");

		    if ($this->getTripID() != "")
			{
			    $this->optionSelectedViewHelper("species by location", "speciesbylocation");
			}

		    if ($this->getTripID() == "" )
			{
				$this->optionSelectedViewHelper("species by date", "chrono");
			}

			if ($this->getMonth() == "" )
			{
				$this->optionSelectedViewHelper("species by month", "speciesbymonth");
			}
			if ($this->getYear() == "" )
			{
				$this->optionSelectedViewHelper("species by year", "speciesbyyear");
			}
		}

	    $this->optionSelectedViewHelper("photo thumbnails", "photo");
	    $this->optionSelectedViewHelper("photo slideshow", "slideshow"); ?>

	    </select>
		</form>
        </div>
<?	}

	function globalMenuBirds()
	{
		if (! strstr(getenv("SCRIPT_NAME"), "species") &&
			$this->getOrderID() == "" && $this->getFamilyID() == "" && $this->getSpeciesID() == "")
		{
?>
			<div class="command-disabled"><a href="./speciesindex.php">birds</a></div>
<?		}
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
			
				<div><?= $orderRequest->command($orderInfo["common_name"]) ?></div>
<?		    }
		
			if ($this->getFamilyID() != "")
			{
				$familyInfo = $this->getFamilyInfo();
				
				$familyRequest = new Request;
				$familyRequest->setPageScript("familydetail.php");
				$familyRequest->setSpeciesID(""); ?>
					 
				<div><?= $familyRequest->command($familyInfo["common_name"]) ?></div>
<?		    }

			if ($this->getSpeciesID() != "")
			{
				$speciesInfo = $this->getSpeciesInfo();
				
				$speciesRequest = new Request;
				$speciesRequest->setPageScript("speciesdetail.php"); ?>
			
				 <div><?= $speciesRequest->command($speciesInfo["common_name"]) ?></div>
<?		    }
?>          </div>
<?		}
	}


	function globalMenuLocations()
	{
		if (! strstr(getenv("SCRIPT_NAME"), "location") &&
			$this->getStateID() == "" && $this->getCounty() == "" && $this->getLocationID() == "")
		{ ?>
			<div class="command-disabled"><a href="./locationindex.php">locations</a></div>
<?		}
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
			
				<div><?= $stateRequest->command($stateInfo["name"]) ?></div>
<?		    }
		
			if ($this->getCounty() != "")
			{
				$countyRequest = new Request;
				$countyRequest->setPageScript("countydetail.php");
				$countyRequest->setLocationID(""); ?>
					 
				<div><?= $countyRequest->command($this->getCounty() . " county") ?></div>
<?		    }

			if ($this->getLocationID() != "")
			{
				$locationInfo = $this->getLocationInfo();
				$locationRequest = new Request;
				$locationRequest->setPageScript("locationdetail.php"); ?>
			
				<div><?= $locationRequest->command($locationInfo["name"]) ?></div>
<?		    }
?>          </div><?
		}
	}

	function globalMenuTrips()
	{
		if (! strstr(getenv("SCRIPT_NAME"), "trip") &&
			$this->getYear() == "" && $this->getMonth() == "" && $this->getTripID() == "")
		{ ?>
			<div class="command-disabled"><a href="./tripindex.php">trips</a></div>
<?		}
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
			
				<div><?= $yearRequest->command($this->getYear()) ?></div>
<?		    }
		
			if ($this->getMonth() != "")
			{
				$monthRequest = new Request;
				$monthRequest->setPageScript("monthdetail.php");
			    $monthRequest->setDayOfMonth("");
				$monthRequest->setTripID(""); ?>
					 
				<div><?= $monthRequest->command(getMonthNameForNumber($this->getMonth())) ?></div>
<?		    }

			if ($this->getTripID() != "")
			{
				$tripInfo = $this->getTripInfo();
				$dayOfMonth = date("jS", strtotime($tripInfo["date"]));
				$tripRequest = new Request;
				$tripRequest->setPageScript("tripdetail.php"); ?>
			
				<div><?= $tripRequest->command($dayOfMonth) ?></div>
<?		    }
?>          </div><?
		}
	}

	function globalMenu()
	{ ?>
		<div id="topleft">
		    <a href="./index.php">
			  <img src="images/topleft1.jpg" align="right" border="0"/>
		    </a>
		</div>

		<div id="contentleft">
			
<?          $this->globalMenuTrips(); ?>
<?          $this->globalMenuBirds(); ?>
<?          $this->globalMenuLocations(); ?>

            <div class="command-spacer">&nbsp;</div>
			<div class="command-simple"><a href="./speciesindex.php?view=chrono">life list</a></div>
	        <div class="command-simple"><a href="./credits.php">about</a></div>

<?	        if (getEnableEdit())
	        { ?>
	        <div class="command-simple"><a href="./tripcreate.php">create trip</a></div>
            <div class="command-simple"><a href="./photosneeded.php">photo todo</a></div>
	        <div class="command-simple"><a href="./errorcheck.php">db todo</a></div>
<?	        } ?>

	        <div class="command-simple"><a href="./indexrss.php">RSS</a></div>
	        <div class="command-simple"><a href="./kmlfile.php?<?= $this->getParams() ?>">KML</a></div>
	        <div class="command-simple"><a href="./timeline.php?<?= $this->getParams() ?>">timeline</a></div>
	
	        <br/>
			<div class="command-simple"><a href="http://sven.spflrc.org:3000/bird_walker/index">NEW V.3 BETA</a></div>
        </div>
<?  }

}
?>
