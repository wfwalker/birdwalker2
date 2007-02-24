<?php

class BirdWalkerQuery
{
	var $mReq;

	function BirdWalkerQuery($inReq)
	{
		$this->mReq = $inReq;
	}

 	function isTripSpecified() { return $this->mReq->isTripSpecified(); }
 	function isSpeciesSpecified() { return $this->mReq->isSpeciesSpecified(); }
    function isFamilySpecified() { return $this->mReq->isFamilySpecified(); }
 	function isOrderSpecified() { return $this->mReq->isOrderSpecified(); }
	function isLocationSpecified() { return $this->mReq->isLocationSpecified(); }
	function isCountySpecified() { return $this->mReq->isCountySpecified(); }
	function isStateSpecified() { return $this->mReq->isStateSpecified(); }
	function isMonthSpecified() { return $this->mReq->isMonthSpecified(); }
	function isYearSpecified() { return $this->mReq->isYearSpecified(); }

	function getPageTitle($inPrefix = "") { return $this->mReq->getPageTitle($inPrefix); }
}