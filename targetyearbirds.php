
<?php

require("./birdwalker.php");

performQuery("CREATE TEMPORARY TABLE tmp ( CommonName varchar(32) default NULL, tripdate date default NULL, sightingCount varchar(32));");
performQuery("INSERT INTO tmp SELECT species.CommonName, max(TripDate), count(sighting.objectid) as sightingCount FROM sighting, species, location where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.State='CA' and Exclude!='1' GROUP BY SpeciesAbbreviation;");
$latestSightingQuery = performQuery("SELECT *, Year(tripdate) as latestYear FROM tmp order by tripdate desc;");

$sightingThreshold = 5;
$targetYear = 2004;
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | Target CA Year Birds</title>
</head>

<body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

<div class="contentright">

<div class="titleblock">
    <div class="pagetitle">Target CA birds for <?= $targetYear ?></div>
	<div class=metadata>Birds we have seen at least <?= $sightingThreshold ?> times, but not seen yet in <?= $targetYear ?></div>
</div>

<table>
<?php

while ($info = mysql_fetch_array($latestSightingQuery))
{
	if ($info["latestYear"] < $targetYear && $info["sightingCount"] >= $sightingThreshold)
	{
?>
		<tr class=report-content>
		<td align=right><?= $info["sightingCount"] ?></td><td> <?= $info["CommonName"] ?></td><td><?= $info["tripdate"] ?></td>
		</tr>
<?
	}
}

performQuery("DROP TABLE tmp;");
?>

</table>

</div>
</body>
</html>


