
<?
require_once("./birdwalker.php");


htmlHead("Counties");

globalMenu();
disabledBrowseButtons();
navTrailLocations();
?>


    <div class=contentright>
      <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
        <div class=pagetitle>Counties</div>
      </div>

<table class=metadata cellpadding=0 cellspacing=0 width="100%">

<?php

$yearArray = null;
$lastStateAccumulated = "NONE";
$prevState = "NONE";
$countyToAccumulate = "NONE";
$countyStats = performQuery("
    SELECT location.County, location.State, location.objectid, COUNT(DISTINCT sighting.SpeciesAbbreviation) AS
      SpeciesCount, year(sighting.TripDate) AS theyear
      FROM location, sighting
      WHERE sighting.LocationName=location.Name
      GROUP BY location.County, theyear
      ORDER BY State, County, theyear"); ?>

    <tr><td></td><? insertYearLabels(); ?></tr>

<?
while ($info = mysql_fetch_array($countyStats))
{
	$county = $info["County"];
	$state = $info["State"];
	$theYear = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];

	if ($lastStateAccumulated != $prevState)
	{ ?>
		<tr>
            <td colspan=11 class=heading>
                <a href="statespecies.php?state=<?= $prevState ?>"><?= getStateNameForAbbreviation($prevState) ?></a>
            </td>
        </tr>

<?		$lastStateAccumulated = $prevState;
	}
		
	if (($yearArray != null) && ($countyToAccumulate != $county))
	{ ?>

		<tr><td class=firstcell>
			<a href="./countydetail.php?state=<?= $prevState ?>&county=<?= urlencode($countyToAccumulate) ?>">
			<?= $countyToAccumulate ?> County
			</a>
			</td>

<?	for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	{ ?>
        <td class=bordered align=right>
            <a href="./specieslist.php?county=<?= urlEncode($countyToAccumulate) ?>&year=<?= $year ?>">
				&nbsp; <?= $yearArray[$year] ?>
            </a>
        </td>
<?	}?>

		</tr>

<?		$yearArray = null;
	}

	$prevState = $state;	
	$countyToAccumulate = $county;
	$yearArray[$theYear] = $speciesCount;
}

 ?>

</table>

<?
footer();
?>

    </div>

<?
htmlFoot();
?>
