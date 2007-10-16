<?php
require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

htmlHead("Counties");

$numCounties = performCount("Count the counties", "SELECT COUNT(DISTINCT(County)) FROM location");

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
    "SELECT location.County, location.State, state.objectid as StateID, location.objectid, COUNT(DISTINCT sighting.SpeciesAbbreviation) AS
      SpeciesCount, year(sighting.TripDate) AS theyear
      FROM location, sighting, state
      WHERE location.id=sighting.location_id AND state.Abbreviation=location.State
      GROUP BY location.County, theyear
      ORDER BY location.State, location.County, theyear");

$countyTotals = performQuery("Get County Totals",
    "SELECT location.County, location.State, state.objectid as StateID, COUNT(DISTINCT sighting.SpeciesAbbreviation) AS SpeciesCount
      FROM location, sighting, state
      WHERE location.id=sighting.location_id AND state.Abbreviation=location.State
      GROUP BY location.County
      ORDER BY location.State, location.County"); ?>

    <tr><td></td><? insertYearLabels(); ?><td class="bordered">Total</td></tr>

<?
while ($info = mysql_fetch_array($countyStats))
{
	$county = $info["County"];
	$stateid = $info["StateID"];
	$theYear = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];

	if ($lastStateAccumulated != $prevState)
	{
		$stateInfo = getStateInfo($prevState); ?>

		<tr>
            <td colspan="12" class="heading">
                <span class="statename"><a href="statedetail.php?stateid=<?= $prevState ?>"><?= $stateInfo["Name"] ?></a></span>
            </td>
        </tr>

<?		$lastStateAccumulated = $prevState;
	}
		
	if (($yearArray != null) && ($countyToAccumulate != $county))
	{ ?>

		<tr><td>
			<a href="./countydetail.php?stateid=<?= $prevState ?>&county=<?= urlencode($countyToAccumulate) ?>">
			<?= $countyToAccumulate ?> County
			</a>
			</td>

<?	for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	{ ?>
        <td class="bordered">
            <a href="./specieslist.php?stateid=<?= $stateid ?>&county=<?= urlEncode($countyToAccumulate) ?>&year=<?= $year ?>">
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
	$countyToAccumulate = $county;
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
