
<?php

require("./birdwalker.php");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | States</title>
  </head>
  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailBirds(); ?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle>State Reports</div>
      </div>

<table columns=11 class=metadata cellpadding=0 cellspacing=0>

<?php

$yearArray = null;
$stateStats = performQuery("select location.State, location.objectid, count(distinct sighting.SpeciesAbbreviation) as SpeciesCount, year(sighting.TripDate) as theyear from location, sighting where sighting.LocationName=location.Name group by location.State, theyear order by State, theyear");

echo "<tr><td></td>"; insertYearLabels(); echo "</tr>";

while ($info = mysql_fetch_array($stateStats))
{
	$state = $info["State"];
	$theYear = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];

	if (($yearArray != null) && ($stateToAccumulate != $state))
	{
		echo "<tr><td class=firstcell><a href=\"./statedetail.php?state=" . urlencode($stateToAccumulate) . "\"/>" . getStateNameForAbbreviation($stateToAccumulate) . "</a></td>";
		for ($year = 1996; $year <= 2004; $year++)
		{
			echo "<td class=bordered align=right>&nbsp;" . $yearArray[$year] . "</td>";
		}
		echo "</tr>";

		$yearArray = null;
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
