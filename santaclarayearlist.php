
<?php

require_once("./birdwalker.php");

$theYear = param($_GET, "year", 2005);

performQuery("CREATE TEMPORARY TABLE tmp ( CommonName varchar(32) default NULL, sightingCount varchar(32));");

performQuery("insert into tmp select species.CommonName, 0 from species");

performQuery("INSERT INTO tmp SELECT species.CommonName, count(sighting.objectid) as sightingCount FROM sighting, species, location where species.Abbreviation=sighting.SpeciesAbbreviation AND sighting.LocationName=location.Name and location.State='CA' and Exclude!='1' and Year(TripDate)='". $theYear . "' GROUP BY SpeciesAbbreviation;");


$latestSightingQuery = performQuery("SELECT countyfrequency.Frequency, tmp.CommonName, max(sightingCount) AS sightingCount FROM tmp, countyfrequency WHERE tmp.CommonName=countyfrequency.CommonName GROUP BY tmp.CommonName ORDER BY countyfrequency.Frequency");

htmlHead("Target Year Birds");

globalMenu();
browseButtons("./santaclarayearlist.php?year=", $theYear, getEarliestYear(), $theYear - 1, $theYear + 1, getLatestYear());
navTrailBirds(); ?>

<div class="contentright">

<div class="titleblock">
    <div class="pagetitle">Target birds for <?= $theYear ?></div>
</div>

<p>
This page shows birds not seen this year along with their frequency rating as found in the annual Santa Clara
year list as maintained by Bill Bousman. That list contains <?= mysql_num_rows($latestSightingQuery) ?> birds.

</p>

<table>
<?php

$prevInfo["Frequency"] = 1;
while ($info = mysql_fetch_array($latestSightingQuery))
{
	if ($info["sightingCount"] == 0)
	{
		if ($prevInfo["Frequency"] != $info["Frequency"]) { ?> <tr><td colspan=2><hr></td></tr> <? }
?>
        <tr class=report-content>
        <td><?= $info["tripdate"] ?><?= $info["Frequency"] ?></td>
        <td><?= $info["CommonName"] ?></td>
		</tr>
<?
		$prevInfo = $info;
		
	}
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
