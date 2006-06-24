<?php

require_once("./birdwalker.php");
require_once("./request.php");

$threshold = 5;

$sortCriteria = "";
array_key_exists("sort", $_GET) && $sortCriteria = $_GET['sort'];

$localtimearray = localtime(time(), 1);
$monthNum = $localtimearray["tm_mon"] + 1;
$dayStart = $localtimearray["tm_yday"] - 3;
$dayStop = $localtimearray["tm_yday"] + 3;

if ($sortCriteria == "") { $sortCriteria = "objectid"; }

$photoCount = performQuery("Count Photos", "
    SELECT species.CommonName, species.objectid, SUM(sighting.Photo) AS theSum, COUNT(sighting.objectid) as theCount
      FROM species, sighting WHERE sighting.SpeciesAbbreviation=species.Abbreviation AND species.ABACountable != '0'
      GROUP BY species.CommonName ORDER BY " . $sortCriteria);

$speciesCount = 0;

htmlHead("Birds in need of photos");

$request = new Request;
$request->globalMenu();
?>

    <div id="topright-photo">
	    <div class="pagesubtitle">Index</div>
	    <div class="pagetitle">Photography Target Species </div>
	</div>

    <div id="contentright">
      <div class="heading">ABA-countable Birds I have seen at least <?= $threshold ?> times but never photographed</div>

<div class="metadata">
	Sort by <a href="./photosneeded.php?sort=objectid">taxo</a> or <a href="./photosneeded.php?sort=theCount+desc">sighting count</a>
</div>

<p>&nbsp;</p>

<table class="report-content">
<tr><td>Species</td><td>Sightings</td></tr>

<?php

	while($info = mysql_fetch_array($photoCount))
	{
		if (($info["theSum"] == "0") && ($info["theCount"] >= $threshold))
		{
?>
        <tr>
          <td>
            <a href="./speciesdetail.php?speciesid=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a>
          </td>
          <td align="right">
            <?= $info["theCount"] ?>
          </td>
        </tr>
<?
			$speciesCount++;
		}
	}
?>

</table>

<p><?= $speciesCount . " species in need of photos" ?></p>

<?
footer();
?>

    </div>

<?
htmlFoot();
?>
