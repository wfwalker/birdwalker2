
<?php

require("./birdwalker.php");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | States</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle>States</div>
      </div>

<table columns=11 class=metadata cellpadding=1 cellspacing=0 width=80%>

<?php

$yearArray = null;
$stateStats = performQuery("SELECT
    location.State,
    location.objectid,
    count(distinct sighting.SpeciesAbbreviation) as SpeciesCount,
    year(sighting.TripDate) as theyear
  FROM location, sighting
  WHERE sighting.LocationName=location.Name
  GROUP BY location.State, theyear
  ORDER BY State, theyear");

?>

  <tr><td></td><? insertYearLabels() ?></tr>

<?
while ($info = mysql_fetch_array($stateStats))
{
	$state = $info["State"];
	$theYear = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];

	if (($yearArray != null) && ($stateToAccumulate != $state))
	{ ?>
		<tr><td class=firstcell>
               <a href="./statespecies.php?state=<?= urlencode($stateToAccumulate) ?>"><?= getStateNameForAbbreviation($stateToAccumulate) ?></a>
            </td>

<?		for ($year = 1996; $year <= 2004; $year++)
		{ ?>
			<td class=bordered align=right>
<?	        if ($yearArray[$year] > 0) { ?>
		        <a href="./specieslist.php?state=<?= urlencode($stateToAccumulate) ?>&year=<?= $year ?>"><?= $yearArray[$year] ?></a>
<?          } else { ?>
                &nbsp;
<?          } ?>
		    </td>
<? 		} ?>

		</tr>

<?		$yearArray = null;
	}

	$prevState = $state;	
	$stateToAccumulate = $state;
	$yearArray[$theYear] = $speciesCount;
}

?>

</table>


    </div>
  </body>
</html>
