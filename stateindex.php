
<?php

require("./birdwalker.php");

$yearArray = null;
$stateStats = performQuery("SELECT
    location.State,
    count(distinct sighting.SpeciesAbbreviation) AS SpeciesCount,
    year(sighting.TripDate) AS theyear
  FROM location, sighting
  WHERE sighting.LocationName=location.Name
  GROUP BY location.State, theyear
  ORDER BY State, theyear");

$years = array(1996, 1997, 1998, 1999, 2000, 2001, 2002, 2003, 2004);
$states = array("AZ", "CA", "IA", "IL", "MA", "NJ", "OR", "PA", "TX", "WI");

while ($info = mysql_fetch_array($stateStats))
{
	$state = $info["State"];
	$year = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];

	$table[$state][$year] = $speciesCount;
}

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

  <tr><td></td><? insertYearLabels() ?></tr>

<? foreach ($states as $state)
   { ?>
    <tr>
      <td class=firstcell><a href="./statespecies.php?state=<?= $state ?>"><?= getStateNameForAbbreviation($state) ?></a></td>
<?	  foreach ($years as $year)
	  { ?>
		  <td class=bordered align=right>
              &nbsp;
              <a href="./specieslist.php?state=<?= urlencode($state) ?>&year=<?= $year ?>"><?= $table[$state][$year] ?></a>
          </td>
<?	  } ?>
    </tr>
<? } ?>

</table>


    </div>
  </body>
</html>
