
<?php

require_once("./birdwalker.php");

performQuery("CREATE TEMPORARY TABLE tmp ( CommonName varchar(32) default NULL, tripdate date default NULL, sightingCount varchar(32));");

performQuery("
    INSERT INTO tmp
    SELECT species.CommonName, max(TripDate), count(sighting.objectid) as sightingCount
    FROM sighting, species, location
    WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND sighting.LocationName=location.Name AND location.State='CA' AND Exclude!='1'
    GROUP BY SpeciesAbbreviation;");

$latestSightingQuery = performQuery("SELECT *, Year(tripdate) as latestYear FROM tmp ORDER BY tripdate desc;");

$sightingThreshold = 5;
$theYear = param($_GET, "year", 2004);
?>

<html>

  <? htmlHead("Target CA Year Birds"); ?>

  <body>

<?php
globalMenu();
browseButtons("./targetyearbirds.php?year=", $theYear, getEarliestYear(), $theYear - 1, $theYear + 1, getLatestYear());
navTrailBirds();
?>

<div class="contentright">

<div class="titleblock">
    <div class="pagetitle">Target CA birds for <?= $theYear ?></div>
	<div class=metadata>Birds we have seen at least <?= $sightingThreshold ?> times, but not seen yet in <?= $theYear ?></div>
</div>

<table>
<?php

while ($info = mysql_fetch_array($latestSightingQuery))
{
	if ($info["latestYear"] < $theYear && $info["sightingCount"] >= $sightingThreshold)
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


