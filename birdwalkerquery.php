<?php

class BirdWalkerQuery
{
	// constrain this query to a particular location
	var $mLocationID;
	// constrain this query to a particular county
	var $mCounty;
	// constrain this query to a particular state
	var $mStateID;

	// constrain this query to a trip
	var $mTripID;
	// constrain this query to a particular month
	var $mMonth;
	// constrain this query to a particular year
	var $mYear;

	// constrain this query to a particular species
	var $mSpeciesID;
	// constrain this query to a particular family
	var $mFamily;
	// constrain this query to a particular order
	var $mOrder;

	function BirdWalkerQuery()
	{
		$this->setLocationID("");
		$this->setCounty("");
		$this->setStateID("");

		$this->setTripID("");
		$this->setMonth("");
		$this->setYear("");

		$this->setFamily("");
		$this->setOrder("");
	}

	function setLocationID($inValue) { $this->mLocationID = $inValue; }
	function setCounty($inValue) { $this->mCounty = $inValue; }
	function setStateID($inValue) { $this->mStateID = $inValue; }

	function setTripID($inValue) { $this->mTripID = $inValue; }
	function setMonth($inValue) { $this->mMonth = $inValue; }
	function setYear($inValue) { $this->mYear = $inValue; }

    function setSpeciesID($inValue) { $this->mSpeciesID = $inValue; }
	function setFamily($inValue) { $this->mFamily = $inValue; }
	function setOrder($inValue) { $this->mOrder = $inValue; }

	function setFromRequest($get)
	{
		$this->setTripID(param($_GET, "tripid", ""));
		$this->setSpeciesID(param($_GET, "speciesid", ""));
		$this->setFamily(param($_GET, "family", ""));
		$this->setOrder(param($_GET, "order", ""));
		$this->setLocationID(param($_GET, "locationid", ""));
		$this->setYear(param($_GET, "year", ""));
		$this->setMonth(param($_GET, "month", ""));
		$this->setCounty(param($_GET, "county", ""));
		$this->setStateID(param($_GET, "stateid", ""));
	}

	function debug()
	{
		echo "\n<!-- locationid " . $this->mLocationID . " county " . $this->mCounty .
			" stateid " . $this->mStateID . " tripid " . $this->mTripID .
			" month " . $this->mMonth . " year " . $this->mYear . 
			" speciesid " . $this->mSpeciesID . " family " . $this->mFamily . " order " . $this->mOrder . " -->\n\n";
	}
}