
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

performQuery("Make Temp Table", "CREATE TEMPORARY TABLE tmp ( CommonName varchar(32) default NULL, objectid varchar(32) default NULL, sightingCount varchar(32));");

performQuery("Put in defaults for all speciesw", "INSERT INTO tmp SELECT species.CommonName, species.objectid, 0 FROM species");

performQuery("Put Santa Clara County Sightings into Tmp",
  "INSERT INTO tmp
    SELECT species.CommonName, species.objectid, count(sighting.objectid) as sightingCount
    FROM sighting, species, location
    WHERE species.Abbreviation=sighting.SpeciesAbbreviation
      AND sighting.LocationName=location.Name and location.State='CA' AND location.County='Santa Clara'
      AND Exclude!='1' and Year(TripDate)='". $request->getYear() . "'
    GROUP BY SpeciesAbbreviation;");

$latestSightingQuery = performQuery("Get Latest Sighting and Frequency",
    "SELECT countyfrequency.Frequency, tmp.CommonName, tmp.objectid, max(sightingCount) AS sightingCount
      FROM tmp, countyfrequency WHERE tmp.CommonName=countyfrequency.CommonName
      GROUP BY tmp.CommonName
      ORDER BY countyfrequency.Frequency");

htmlHead("Target Year Birds");

$request->globalMenu();
?>

<div id="topright-trip">
	<? browseButtons("Year List", "./santaclarayearlist.php?year=", $request->getYear(), $request->getYear() - 1, $request->getYear() - 1, $request->getYear() + 1, $request->getYear() + 1); ?> 
    <div class="pagetitle">Target birds for <?= $request->getYear() ?></div>
</div>

<div id="contentright">

<p>
This page shows birds not seen in Santa Clara County for the given
year along with their frequency rating as found in the annual Santa
Clara year list as maintained by Bill Bousman.
That list contains <?= mysql_num_rows($latestSightingQuery) ?> birds.
</p>

<table>
<?php

$prevInfo["Frequency"] = 1;
while ($info = mysql_fetch_array($latestSightingQuery))
{
	if ($info["sightingCount"] == 0)
	{
		if ($prevInfo["Frequency"] != $info["Frequency"]) { ?> <tr><td colspan="2"><hr></td></tr> <? }
?>
        <tr class="report-content">
        <td><?= $info["Frequency"] ?></td>
        <td><a href="./speciesdetail.php?view=locationsbyyear&speciesid=<?= $info["objectid"] ?>"/><?= $info["CommonName"] ?></a></td>
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
