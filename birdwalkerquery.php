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
		return ($this->mReq->getTripID() == '');
	}

 	function isSpeciesSpecified()
	{
		return ($this->mReq->getSpeciesID() == '');
	}

 	function isFamilySpecified()
	{
		return ($this->mReq->getFamilyID() == '');
	}
 
 	function isOrderSpecified()
	{
		return ($this->mReq->getOrderID() == '');
	}

	function isLocationSpecified()
	{
		return ($this->mReq->getLocationID() == '');
	}

	function getPageTitle($inPrefix = "")
	{
		if ($inPrefix != "") {
			$pageTitleItems[] = $inPrefix;
		}

		if (! $this->isSpeciesSpecified()) {
			$speciesInfo = $this->mReq->getSpeciesInfo();
			$pageTitleItems[] = $speciesInfo["CommonName"];
		} elseif (! $this->isFamilySpecified()) {
			$familyInfo = $this->mReq->getFamilyInfo();
			$pageTitleItems[] = $familyInfo["LatinName"];
		} elseif (! $this->isOrderSpecified()) {
			$orderInfo = $this->mReq->getOrderInfo();
			$pageTitleItems[] = $orderInfo["LatinName"];
		}

		if (! $this->isLocationSpecified()) {
			$locationInfo = $this->mReq->getLocationInfo();
			$pageTitleItems[] = $locationInfo["Name"];
		} elseif (! $this->isCountySpecified()) {
			$pageTitleItems[] = $this->mReq->getCounty() . " County";
		} elseif (! $this->isStateSpecified()) {
			$stateInfo = $this->mReq->getStateInfo();
		    $pageTitleItems[] = $stateInfo["Name"];
		}

		if ($this->mReq->getMonth() !="") {
			$pageTitleItems[] = getMonthNameForNumber($this->mReq->getMonth());
		}
		if ($this->mReq->getYear() !="") {
			$pageTitleItems[] = $this->mReq->getYear();
		}

		return implode(", ", $pageTitleItems);
	}
}