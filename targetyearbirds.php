
<?php

require_once("./birdwalker.php");
require_once("./request.php");

performQuery("Create Temp Table",
    "CREATE TEMPORARY TABLE tmp (
      CommonName varchar(32) default NULL,
      speciesid varchar(32) default NULL,
      tripdate date default NULL,
      sightingCount varchar(32));");

performQuery("Count Sightings for Species",
    "INSERT INTO tmp
      SELECT species.CommonName, species.objectid, max(TripDate), count(sighting.objectid) as sightingCount
      FROM sighting, species, location
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND
        sighting.LocationName=location.Name AND location.State='CA' AND Exclude!='1'
      GROUP BY SpeciesAbbreviation;");

$latestSightingQuery = performQuery("Find Latest Sighting",
    "SELECT *, Year(tripdate) as latestYear, " . shortNiceDateColumn("tripdate") . "
      FROM tmp
      ORDER BY tripdate desc;");

$sightingThreshold = 10;

htmlHead("Target CA Year Birds");

$request = new Request;
$request->globalMenu();
?>

<div class="topright-species">
	<div class="pagesubtitle">Index</div>
    <div class="pagetitle">Target CA birds for <?= getLatestYear() ?></div>
</div>

<div class="contentright">
	<p class="metadata">
        Birds we have seen at least <?= $sightingThreshold ?> times, but not seen yet in <?= getLatestYear() ?>
    </p>

<table>
	<tr class="report-content"><td>Sightings</td><td>Name</td><td>Last Seen</td></tr>

<?
while ($info = mysql_fetch_array($latestSightingQuery))
{
	if ($info["latestYear"] < getLatestYear() && $info["sightingCount"] >= $sightingThreshold)
	{
?>
		<tr class="report-content">
		  <td align="right"><a href="./sightinglist.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["sightingCount"] ?></a></td>
          <td><a href="./speciesdetail.php?speciesid=<?= $info["speciesid"] ?>"><?= $info["CommonName"] ?></a></td>
          <td><?= $info["niceDate"] ?></td>
		</tr>
<?
	}
}

performQuery("Clean up tmp Table", "DROP TABLE tmp;");
?>

</table>

<?
footer();
?>

</div>

<?
htmlFoot();
?>

