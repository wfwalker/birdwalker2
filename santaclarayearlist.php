
<?php

require("./birdwalker.php");

$theYear = param($_GET, "year", 2004);

performQuery("CREATE TEMPORARY TABLE tmp ( CommonName varchar(32) default NULL, sightingCount varchar(32));");

performQuery("insert into tmp select species.CommonName, 0 from species");

performQuery("INSERT INTO tmp SELECT species.CommonName, count(sighting.objectid) as sightingCount FROM sighting, species, location where species.Abbreviation=sighting.SpeciesAbbreviation AND sighting.LocationName=location.Name and location.State='CA' and Exclude!='1' and Year(TripDate)='". $theYear . "' GROUP BY SpeciesAbbreviation;");


$latestSightingQuery = performQuery("select countyfrequency.Frequency, tmp.CommonName, max(sightingCount) as sightingCount from tmp, countyfrequency where tmp.CommonName=countyfrequency.CommonName group by tmp.CommonName order by countyfrequency.Frequency");

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | Target Year Birds</title>
</head>

<body>

<?php
globalMenu();
browseButtons("./santaclarayearlist.php?year=", $theYear, getEarliestYear(), $theYear - 1, $theYear + 1, getLatestYear());
navTrailBirds(); ?>

<div class="contentright">

<div class="titleblock">
    <div class="pagetitle">Target birds for <?= $theYear ?></div>
	<div class=metadata><?= mysql_num_rows($latestSightingQuery) ?> santa clara year list</div>
</div>

<table>
<?php

while ($info = mysql_fetch_array($latestSightingQuery))
{
	if ($info["sightingCount"] == 0)
	{
?>
        <tr class=report-content>
        <td align=right><?= $info["sightingCount"] ?></td>
        <td><?= $info["CommonName"] ?></td>
        <td><?= $info["tripdate"] ?><?= $info["Frequency"] ?></td></tr>
<?
	}
}
?>

</table>

</div>
</body>
</html>


