
<?php

require_once("./birdwalker.php");

$photoCount = performCount("select count(*) from sighting where Photo='1'");
$photoSpeciesCount = performCount("select count(distinct(sighting.SpeciesAbbreviation)) from sighting where Photo='1'");

htmlHead("Photo List");

globalMenu();
disabledBrowseButtons();
navTrailPhotos();
?>


    <div class=contentright>
	  <div class="pagesubtitle">Index</div>
      <div class="titleblock">	  
	    <div class=pagetitle>Photos</div>
        <div class=metadata>
          <a href="./photoindextaxo.php">by species</a> |
          <a href="./photoindex.php">by date</a> |
          <a href="./photoindexlocation.php">by location</a>
        </div>
      </div>

<div class=heading><?= $photoCount . " photos covering " . $photoSpeciesCount . " species"; ?></div>

<table cellpadding=4>

<?php
$photoQuery = performQuery("
  SELECT CONCAT(sighting.TripDate, sighting.objectid) AS photoOrder, sighting.*,
      species.CommonName, DATE_FORMAT(sighting.TripDate, '%M %e, %Y') AS niceDate
    FROM sighting, species WHERE Photo='1' AND sighting.SpeciesAbbreviation=species.Abbreviation
    ORDER BY photoOrder DESC");


$counter = 0;

while($info = mysql_fetch_array($photoQuery))
{
	if (($counter % 2) == 0)
	{
?>      <tr> <?
    }
?>
    <td valign=top align=right><?= getThumbForSightingInfo($info) ?></td>
    <td class=report-content valign=top>
	    <?= $info["CommonName"] ?><br/>
        <?= $info["niceDate"] ?><br/>
        <?= $info["LocationName"] ?><br/>
        <?= $info[""] ?></td>
<?
    if (($counter % 4) == 1)
	{
?>      </tr> <?
    }

    $counter++;
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