<?php

require_once("./birdwalker.php");
require_once("./request.php");

$yearArray = null;
$stateStats = performQuery("Get State Statistics",
  "SELECT
    location.State,
    count(distinct sighting.species_id) AS SpeciesCount,
    year(trip.Date) AS theyear
  FROM location, sighting, trip
  WHERE sighting.location_id=location.id AND sighting.trip_id=trip.id
  GROUP BY location.State, theyear
  ORDER BY State, theyear");

// todo could build some kind of set of visited states while running through the stateStats?
$visitedStatesQuery = performQuery("Get List of visited States",
    "SELECT DISTINCT(state.id)
      FROM state, location, sighting
      WHERE state.Abbreviation=location.State and location.id=sighting.location_id ORDER BY state.Name");

while ($info = mysql_fetch_array($stateStats))
{
	$state = $info["State"];
	$year = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];

	$table[$state][$year] = $speciesCount;
}

htmlHead("States");

$request = new Request;
$request->globalMenu();
?>

    <div id="topright-location">
      <div class="pagekind">Index</div>
      <div class="pagetitle">States</div>
	</div>

    <div id="contentright">

    <div class="heading">
      Species seen in each State by Year
    </p>

<table class="report-content" cellpadding="1" cellspacing="0" width="80%">

  <tr><td></td><? insertYearLabels() ?></tr>

<? while ($info = mysql_fetch_array($visitedStatesQuery))
   { $id = $info["id"];
	 $info = getStateInfo($id);
     $state = $info["Abbreviation"]; ?>
    <tr>
      <td><a href="./statedetail.php?stateid=<?= $info["id"] ?>"><?= getStateNameForAbbreviation($state) ?></a></td>
<?	  for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	  { ?>
		  <td class="bordered" align="right">
              &nbsp;
<?
		if (array_key_exists($year, $table[$state]))
	    {
			echo "<a href=\"./specieslist.php?stateid=". $id . "&year=" . $year  . "\">" . $table[$state][$year] . "</a>";
		} ?>

         </td>
<?	  } ?>
    </tr>
<? } ?>

</table>

<?
footer();
?>

    </div>

<?
htmlFoot();
?>
