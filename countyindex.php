
<?php

require("./birdwalker.php");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Counties</title>
  </head>
  <body>

<?php navigationHeader() ?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle>County Reports</div>
      </div>

<table class=metadata cellpadding=0 cellspacing=0 width="100%">

<?php

$yearArray = null;
$lastStateAccumulated = "NONE";
$prevState = "NONE";
$countyToAccumulate = "NONE";
$countyStats = performQuery("select location.County, location.State, location.objectid, count(distinct sighting.SpeciesAbbreviation) as SpeciesCount, year(sighting.TripDate) as theyear from location, sighting where sighting.LocationName=location.Name group by location.County, theyear order by State, County, theyear");

while ($info = mysql_fetch_array($countyStats))
{
	$county = $info["County"];
	$state = $info["State"];
	$theYear = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];

	if ($lastStateAccumulated != $prevState)
	{
		echo "<tr><td>" . $prevState . "</td>"; insertYearLabels(); echo "</tr>";
		$lastStateAccumulated = $prevState;
	}
		
	if (($yearArray != null) && ($countyToAccumulate != $county))
	{
		echo "<tr><td class=firstcell><a href=\"./countydetail.php?county=" . urlencode($countyToAccumulate) . "\"/>" . $countyToAccumulate . " County</a></td>";
		for ($year = 1996; $year <= 2004; $year++)
		{
			echo "<td class=bordered>&nbsp;" . $yearArray[$year] . "</td>";
		}
		echo "</tr>";

		$yearArray = null;
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
