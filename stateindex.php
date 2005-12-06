
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$yearArray = null;
$stateStats = performQuery("Get State Statistics",
  "SELECT
    location.State,
    count(distinct sighting.SpeciesAbbreviation) AS SpeciesCount,
    year(sighting.TripDate) AS theyear
  FROM location, sighting
  WHERE sighting.LocationName=location.Name
  GROUP BY location.State, theyear
  ORDER BY State, theyear");

// todo could build some kind of set of visited states while running through the stateStats?
$visitedStatesQuery = performQuery("Get List of visited States",
    "SELECT DISTINCT(state.objectid)
      FROM state, location, sighting
      WHERE state.Abbreviation=location.State and location.Name=sighting.LocationName ORDER BY state.Name");

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

    <div class="topright">
      <div class="pagesubtitle">Index</div>
      <div class="pagetitle">States</div>
	</div>

    <div class="contentright">

<table class=metadata cellpadding=1 cellspacing=0 width=80%>

  <tr><td></td><? insertYearLabels() ?></tr>

<? while ($info = mysql_fetch_array($visitedStatesQuery))
   { $id = $info["objectid"];
	 $info = getStateInfo($id);
     $state = $info["Abbreviation"]; ?>
    <tr>
      <td class=firstcell><a href="./statedetail.php?stateid=<?= $info["objectid"] ?>"><?= getStateNameForAbbreviation($state) ?></a></td>
<?	  for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	  { ?>
		  <td class=bordered align=right>
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
