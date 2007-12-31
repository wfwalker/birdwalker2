<?php
require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

htmlHead("Counties");

$numCounties = performCount("Count the counties", "SELECT COUNT(DISTINCT(county_id)) FROM locations");

$request->globalMenu();
?>

    <div id="topright-location">
      <div class="pagekind">Index</div>
        <div class="pagetitle">Counties</div>
	</div>

    <div id="contentright">

  <div class="heading"><?= $numCounties ?> Counties</div>

  <div class="onecolumn">
<table class="report-content" cellpadding="0" cellspacing="0" width="100%">

<?php

$yearArray = null;
$lastStateAccumulated = "NONE";
$prevState = "NONE";
$countyToAccumulate = "NONE";
$countyStats = performQuery("Get County Statistics By Year",
    "SELECT locations.county_id, states.id as StateID, locations.id, COUNT(DISTINCT sightings.species_id) AS
      SpeciesCount, year(trips.date) AS theyear
      FROM locations, sightings, counties, states, trips
      WHERE locations.id=sightings.location_id
		AND sightings.trip_id=trips.id
		AND locations.county_id=counties.id
		AND counties.state_id=states.id
      GROUP BY locations.county_id, theyear
      ORDER BY locations.county_id, locations.county_id, theyear");

$countyTotals = performQuery("Get County Totals",
    "SELECT locations.county_id, states.id as StateID, COUNT(DISTINCT sightings.species_id) AS SpeciesCount
      FROM locations, counties, sightings, states
      WHERE locations.id=sightings.location_id and locations.county_id=counties.id AND counties.state_id=states.id
      GROUP BY locations.county_id
      ORDER BY locations.county_id"); ?>

    <tr><td></td><? insertYearLabels(); ?><td class="bordered">Total</td></tr>

<?
while ($info = mysql_fetch_array($countyStats))
{
	$countyInfo = getCountyInfo($info["county_id"]);
	$stateid = $info["StateID"];
	$theYear = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];

	if ($lastStateAccumulated != $prevState)
	{
		$stateInfo = getStateInfo($prevState); ?>

		<tr>
            <td colspan="12" class="heading">
                <span class="statename"><a href="statedetail.php?stateid=<?= $prevState ?>"><?= $stateInfo["name"] ?></a></span>
            </td>
        </tr>

<?		$lastStateAccumulated = $prevState;
	}
		
	if (($yearArray != null) && ($countyToAccumulate["id"] != $countyInfo["id"]))
	{ ?>

		<tr><td>
			<a href="./countydetail.php?countyid=<?= $countyToAccumulate['id'] ?>">
			<?= $countyToAccumulate["name"] ?> County
			</a>
			</td>

<?	for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	{ ?>
        <td class="bordered">
            <a href="./specieslist.php?countyid=<?= $countyToAccumulate['id'] ?>&year=<?= $year ?>">
	          &nbsp; <? if (array_key_exists($year, $yearArray)) echo $yearArray[$year]; ?>
            </a>
        </td>
<?	}

	$countyTotal = mysql_fetch_array($countyTotals); ?>

		 <td class="bordered"><?= $countyTotal["SpeciesCount"] ?></td>

		</tr>

<?		$yearArray = null;
	}

	$prevState = $stateid;	
	$countyToAccumulate = $countyInfo;
	$yearArray[$theYear] = $speciesCount;
}

 ?>

</table>

<?
footer();
?>

    </div>
</div>
<?
htmlFoot();
?>
