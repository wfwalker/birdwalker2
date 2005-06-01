<?php

class Request
{
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

	function Request()
	{
		$this->setLocationID(param($_GET, "locationid", ""));
		$this->setCounty(param($_GET, "county", ""));
		$this->setStateID(param($_GET, "stateid", ""));

		$this->setTripID(param($_GET, "tripid", ""));
		$this->setMonth(param($_GET, "month", ""));
		$this->setYear(param($_GET, "year", ""));

		$this->setSpeciesID(param($_GET, "speciesid", ""));
		$this->setFamilyID(param($_GET, "familyid", ""));
		$this->setOrderID(param($_GET, "orderid", ""));

		$this->setMapHeight(param($_GET, "height", "320"));
		$this->setMapWidth(param($_GET, "width", "640"));
		$this->setLatitude(param($_GET, "lat", ""));
		$this->setLongitude(param($_GET, "long", ""));
		$this->setScale(param($_GET, "scale", "1.0"));
		$this->setBackground(param($_GET, "backgnd", "roads"));

		echo "<!-- constructed Request -->";
		$this->debug();
	}

	function setLocationID($inValue) { $this->mLocationID = $inValue; }
	function getLocationID() { return $this->mLocationID; }
	function setCounty($inValue) { $this->mCounty = $inValue; }
	function getCounty() { return $this->mCounty; }
	function setStateID($inValue) { $this->mStateID = $inValue; }
	function getStateID() { return $this->mStateID; }

	function setTripID($inValue) { $this->mTripID = $inValue; }
	function getTripID() { return $this->mTripID; }
	function setMonth($inValue) { $this->mMonth = $inValue; }
	function getMonth() { return $this->mMonth; }
	function setYear($inValue) { $this->mYear = $inValue; }
	function getYear() { return $this->mYear; }

    function setSpeciesID($inValue) { $this->mSpeciesID = $inValue; }
    function getSpeciesID() { return $this->mSpeciesID; }
	function setFamilyID($inValue) { $this->mFamilyID = $inValue; $this->mOrderID = floor($invalue / 100); }
	function getFamilyID() { return $this->mFamilyID;  }
	function setOrderID($inValue) { $this->mOrderID = $inValue; }
	function getOrderID() { return $this->mOrderID; }

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
		$params = "";

		if ($this->mLocationID != "") {
			$params = $params . "&locationid=" . $this->mLocationID;
		}
		if ($this->mCounty != "") {
			$params = $params . "&county=" . $this->mCounty;
		}
		if ($this->mStateID != "") {
			$params = $params . "&stateid=" . $this->mStateID;
		}

		if ($this->mTripID != "") {
			$params = $params . "&tripid=" . $this->mTripID;
		}

		if ($this->mSpeciesID != "") {
			$params = $params . "&speciesid=" . $this->mSpeciesID;
		} elseif ($this->mFamilyID != "") {
			$params = $params . "&familyid=" . $this->mFamilyID;
		} elseif ($this->mOrderID != "") {
			$params = $params . "&orderid=" . $this->mOrderID;
		}
		
		if ($this->mMonth !="") {
			$params = $params . "&month=" . $this->mMonth;
		}
		if ($this->mYear !="") {
			$params = $params . "&year=" . $this->mYear;
		}

		return $params;
	}

	function debug()
	{
		echo "\n<!-- locationid " . $this->mLocationID . " county " . $this->mCounty .
			" stateid " . $this->mStateID . " tripid " . $this->mTripID .
			" month " . $this->mMonth . " year " . $this->mYear . 
			" speciesid " . $this->mSpeciesID . " family " . $this->mFamilyID . " order " . $this->mOrderID . " -->\n\n";
	}
}