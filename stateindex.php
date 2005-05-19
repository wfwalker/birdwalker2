
<?php

require_once("./birdwalker.php");

$yearArray = null;
$stateStats = performQuery("SELECT
    location.State,
    count(distinct sighting.SpeciesAbbreviation) AS SpeciesCount,
    year(sighting.TripDate) AS theyear
  FROM location, sighting
  WHERE sighting.LocationName=location.Name
  GROUP BY location.State, theyear
  ORDER BY State, theyear");

// todo could build some kind of set of visited states while running through the stateStats?
$visitedStatesQuery = performQuery("
    SELECT DISTINCT(state.objectid)
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

globalMenu();
navTrailLocations("list");
?>

    <div class=contentright>
      <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
        <div class=pagetitle>States</div>
      </div>

<table class=metadata cellpadding=1 cellspacing=0 width=80%>

  <tr><td></td><? insertYearLabels() ?></tr>

<? while ($info = mysql_fetch_array($visitedStatesQuery))
   { $id = $info["objectid"];
	 $info = getStateInfo($id);
     $state = $info["Abbreviation"]; ?>
    <tr>
      <td class=firstcell><a href="./statedetail.php?id=<?= $info["objectid"] ?>"><?= getStateNameForAbbreviation($state) ?></a></td>
<?	  for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	  { ?>
		  <td class=bordered align=right>
              &nbsp;
              <a href="./specieslist.php?stateid=<?= $id ?>&year=<?= $year ?>"><?= $table[$state][$year] ?></a>
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
