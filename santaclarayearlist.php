<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

performQuery("Make Temp Table", "CREATE TEMPORARY TABLE tmp ( common_name varchar(32) default NULL, id varchar(32) default NULL, sightingCount varchar(32));");

performQuery("Put in defaults for all species", "INSERT INTO tmp SELECT species.common_name, species.id, 0 FROM species");

performQuery("Put Santa Clara County Sightings into Tmp",
  "INSERT INTO tmp
    SELECT species.common_name, species.id, count(sightings.id) as sightingCount
    FROM sightings, species, locations, trips
    WHERE species.id=sightings.species_id
	  AND trips.id=sightings.trip_id
      AND sightings.location_id=locations.id and locations.county_id='2'
      AND Exclude!='1' " . ($request->isYearSpecified() ? "and Year(trips.Date)='". $request->getYear() . "'" : "" ) . "
    GROUP BY species.id ORDER BY species.id;");

$latestSightingQuery = performQuery("Get Latest Sighting and Frequency",
    "SELECT countyfrequency.Frequency, tmp.common_name, tmp.id, max(sightingCount) AS sightingCount
      FROM tmp, countyfrequency WHERE tmp.common_name=countyfrequency.common_name
      GROUP BY tmp.common_name
      ORDER BY countyfrequency.Frequency, tmp.id");

htmlHead("Target Year Birds");

$request->globalMenu();
?>

<div id="topright-trip">
	<? browseButtons("Year List", "./santaclarayearlist.php?year=", $request->getYear(), $request->getYear() - 1, $request->getYear() - 1, $request->getYear() + 1, $request->getYear() + 1); ?> 
    <div class="pagetitle">Target birds for <?= $request->isYearSpecified() ? $request->getYear() : "Santa Clara County" ?></div>
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
        <td><a href="./speciesdetail.php?view=locationsbyyear&speciesid=<?= $info["id"] ?>"/><?= $info["common_name"] ?></a></td>
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
