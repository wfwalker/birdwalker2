<?php

require("./birdwalker.php");

$threshold = 10;

$sortCriteria = $_GET['sort'];
$localtimearray = localtime(time(), 1);
$monthNum = $localtimearray["tm_mon"] + 1;
$dayStart = $localtimearray["tm_yday"] - 3;
$dayStop = $localtimearray["tm_yday"] + 3;

if ($sortCriteria == "") { $sortCriteria = "theCount desc"; }

$photoCount = performQuery("select species.CommonName, species.objectid, sum(sighting.Photo) as theSum, count(sighting.objectid) as theCount from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation group by species.CommonName order by " . $sortCriteria);

$speciesCount = 0;

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Birds in need of photos</title>
  </head>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailPhotos("missing");
?>

    <div class="contentright">
	  <div class=titleblock>
	    <div class=pagetitle>Birds in need of photos</div>
        <div class=pagesubtitle>Birds seen at least <?= $threshold ?> times with no photo</div>
      </div>

<a href="./photosneeded.php?sort=objectid">taxo</a>
| <a href="./photosneeded.php?sort=theCount+desc">sighting count</a>
| <a href="./photosneeded.php?sort=CommonName">alphabetical</a>

<table columns=2 class=report-content>
<tr class=titleblock><td>Species</td><td>Sightings</td></tr>

<?php

	while($info = mysql_fetch_array($photoCount))
	{
		if (($info["theSum"] == "0") && ($info["theCount"] >= $threshold))
		{
?>
        <tr>
          <td>
            <a href="./speciesdetail.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a>
          </td>
          <td align=right>
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

    </div>
  </body>

</html>