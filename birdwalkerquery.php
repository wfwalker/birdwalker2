<?php

class BirdWalkerQuery
{
	var $mReq;

	function BirdWalkerQuery($inReq)
	{
		$this->mReq = $inReq;
	}

	function getPageTitle($inPrefix = "")
	{
		if ($inPrefix != "") {
			$pageTitleItems[] = $inPrefix;
		}

		if ($this->mReq->getSpeciesID() != "") {
			$speciesInfo = getSpeciesInfo($this->mReq->getSpeciesID());
			$pageTitleItems[] = $speciesInfo["CommonName"];
		} elseif ($this->mReq->getFamilyID() != "") {
			$familyInfo = getFamilyInfo($this->mReq->getFamilyID() * pow(10, 7));
			$pageTitleItems[] = $familyInfo["LatinName"];
		} elseif ($this->mReq->getOrder() != "") {
			$orderInfo = getOrderInfo($this->mReq->getOrder() * pow(10, 9));
			$pageTitleItems[] = $orderInfo["LatinName"];
		}

		if ($this->mReq->getLocationID() != "") {
			$locationInfo = getLocationInfo($this->mReq->getLocationID()); 
			$pageTitleItems[] = $locationInfo["Name"];
		} elseif ($this->mReq->getCounty() != "") {
			$pageTitleItems[] = $this->mReq->getCounty() . " County";
		} elseif ($this->mReq->getStateID() != "") {
			$stateInfo = getStateInfo($this->mReq->getStateID());
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