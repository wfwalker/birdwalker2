
<? require("./birdwalker.php"); ?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Counties</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
pageThumbnail("SELECT *, rand() AS shuffle FROM sighting WHERE Photo='1' ORDER BY shuffle");
?>

<div class=navigationright><a href="./index.php">birdWalker</a> &gt; <a href="./locationindex.php">locations</a> &gt; counties by year</div>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle>County Reports</div>
      </div>

<table columns=11 class=metadata cellpadding=0 cellspacing=0 width="100%">

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
			<a href="./countyspecies.php?state=<?= $prevState ?>&county=<?= urlencode($countyToAccumulate) ?>"/>
			<?= $countyToAccumulate ?> County
			</a>
			</td>

<?	for ($year = 1996; $year <= 2004; $year++)
	{ ?>
        <td class=bordered align=right>
            <a href="./specieslist.php?county=<?= urlEncode($countyToAccumulate) ?>&year=<?= $year ?>">
				&nbsp; <?= $yearArray[$year] ?>
            <a/>
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


    </div>
  </body>
</html>
