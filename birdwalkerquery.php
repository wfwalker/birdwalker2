<?php

class BirdWalkerQuery
{
	var $mReq;

	function BirdWalkerQuery($inReq)
	{
		$this->mReq = $inReq;
	}

 	function isTripSpecified()
	{
		if ($this->mReq->getTripID() == '') return false; else return true;
	}

 	function isSpeciesSpecified()
	{
		if ($this->mReq->getSpeciesID() == "") return false; else return true;
	}

 	function isFamilySpecified()
	{
		if ($this->mReq->getFamilyID() == '') return false; else return true;
	}
 
 	function isOrderSpecified()
	{
		if ($this->mReq->getOrderID() == '') return false; else return true;
	}

	function isLocationSpecified()
	{
		if ($this->mReq->getLocationID() == '') return false; else return true;
	}

	function isCountySpecified()
	{
		if ($this->mReq->getCounty() == '') return false; else return true;
	}

	function isStateSpecified()
	{
		if ($this->mReq->getStateID() == '') return false; else return true;
	}

	function getPageTitle($inPrefix = "")
	{
		if ($inPrefix != "") {
			$pageTitleItems[] = $inPrefix;
		}

		if ($this->isSpeciesSpecified()) {
			$speciesInfo = $this->mReq->getSpeciesInfo();
			$pageTitleItems[] = $speciesInfo["CommonName"];
		} elseif ($this->isFamilySpecified()) {
			$familyInfo = $this->mReq->getFamilyInfo();
			$pageTitleItems[] = $familyInfo["LatinName"];
		} elseif ($this->isOrderSpecified()) {
			$orderInfo = $this->mReq->getOrderInfo();
			$pageTitleItems[] = $orderInfo["LatinName"];
		}

		if ($this->isLocationSpecified()) {
			$locationInfo = $this->mReq->getLocationInfo();
			$pageTitleItems[] = $locationInfo["Name"];
		} elseif ($this->isCountySpecified()) {
			$pageTitleItems[] = $this->mReq->getCounty() . " County";
		} elseif ($this->isStateSpecified()) {
			$stateInfo = $this->mReq->getStateInfo();
		    $pageTitleItems[] = $stateInfo["Name"];
		}

		if ($this->mReq->getMonth() != "") {
			$pageTitleItems[] = getMonthNameForNumber($this->mReq->getMonth());
		}
		if ($this->mReq->getYear() != "") {
			$pageTitleItems[] = $this->mReq->getYear();
		}

		return implode(", ", $pageTitleItems);
	}
}