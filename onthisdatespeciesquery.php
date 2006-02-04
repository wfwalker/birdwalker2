<?php

require_once("./birdwalkerquery.php");
require_once("./speciesquery.php");

class OnThisDateSpeciesQuery extends SpeciesQuery
{
	function OnThisDateSpeciesQuery($inReq)
	{
		$this->SpeciesQuery($inReq);
	}


	function getWhereClause()
	{
 		$localtimearray = localtime(time(), 1);
 		$monthNum = $localtimearray["tm_mon"] + 1;
 		$dayNum = $localtimearray["tm_mday"];

		$whereClause = SpeciesQuery::getWhereClause();
		$whereClause = $whereClause . " AND Month(sighting.TripDate)='" . $monthNum . "' AND DayOfMonth(sighting.TripDate)='" . $dayNum . "'";

		return $whereClause;
	}
}

?>
