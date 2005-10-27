
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$photoSpecies = performQuery("Find Species With Photos",
    "SELECT DISTINCT species.*, COUNT(DISTINCT sighting.objectid) AS photoCount, max(sighting.TripDate) as latestPhoto
      FROM species, sighting
      WHERE species.Abbreviation=sighting.SpeciesAbbreviation AND sighting.Photo='1'
      GROUP BY sighting.SpeciesAbbreviation ORDER BY species.objectid");
$photoCount = performCount("Count Photos",
    "SELECT COUNT(*) FROM sighting WHERE Photo='1'");

$thresholdTime = strtotime("-1 month");

htmlHead("Photo List");

$request = new Request;
$request->globalMenu();
?>

    <div class="topright">
	  <div class="pagesubtitle">Index</div>
	  <div class="pagetitle">Photos</div>
	</div>

    <div class=contentright>
      <div class="titleblock">
        <div class="viewlinks">
          <a href="./photoindextaxo.php">by species<a/> |
          <a href="./photoindex.php">by date</a> |
          <a href="./photoindexlocation.php">by location</a>
        </div>
      </div>

   <div class=heading><?= $photoCount ?> photos covering <?= mysql_num_rows($photoSpecies) ?> species</div>

<table width="100%">
<tr valign=top><td width="50%" class=report-content>
<?
$counter = round(mysql_num_rows($photoSpecies)  * 0.6);

$prevInfo = "";
while($info = mysql_fetch_array($photoSpecies))
{
	$orderNum =  floor($info["objectid"] / pow(10, 9));
	
	if ($prevInfo == "" || getFamilyIDFromSpeciesID($prevInfo["objectid"]) != getFamilyIDFromSpeciesID($info["objectid"]))
	{
		$taxoInfo = getFamilyInfoFromSpeciesID($info["objectid"]); ?>
		<div class="subheading"><?= strtolower($taxoInfo["LatinName"]) ?></div>
<?	} ?>

    <div>
        <a href="./speciesdetail.php?view=photo&speciesid=<?= $info["objectid"] ?>">
             <?= $info["CommonName"] ?>
        </a>
        <? if ($info["photoCount"] > 1) echo "(" . $info["photoCount"] . ")"; ?>
        <? if (strtotime($info["latestPhoto"]) > $thresholdTime) echo "NEW"; ?>
	</div>

<?	$prevInfo = $info;
    $counter--;
    if ($counter == 0) { ?></td><td width="50%" class=report-content> <? }
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
