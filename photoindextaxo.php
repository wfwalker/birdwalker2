<?php

require_once("./birdwalker.php");
require_once("./request.php");

$photoSpecies = performQuery("Find Species With Photos",
    "SELECT DISTINCT species.*, COUNT(DISTINCT sighting.id) AS photoCount, max(trip.Date) as latestPhoto
      FROM species, sighting, trip
      WHERE trip.id=sighting.trip_id AND species.id=sighting.species_id AND sighting.Photo='1' AND species.ABACountable != '0'
      GROUP BY sighting.species_id ORDER BY species.id");
$photoCount = performCount("Count Photos",
    "SELECT COUNT(*) FROM sighting WHERE Photo='1'");

$thresholdTime = strtotime("-1 month");

htmlHead("Photo List");

$request = new Request;
$request->globalMenu();
?>

    <div id="topright-photo">
	  <div class="pagekind">Index</div>
	  <div class="pagetitle">Photos</div>
      <div class="pagesubtitle">
      <a href="./photoindextaxo.php">by species</a> |
      <a href="./photoindex.php">by date</a> |
      <a href="./photoindexlocation.php">by location</a>
      </div>
	</div>

    <div id="contentright">
      <div class="heading"><?= $photoCount ?> photos covering <?= mysql_num_rows($photoSpecies) ?> ABA-countable species</div>

<table width="100%" class="report-content">
<tr valign="top"><td width="50%" class="leftcolumn">
<?
$counter = round(mysql_num_rows($photoSpecies)  * 0.6);

$prevInfo = "";
while($info = mysql_fetch_array($photoSpecies))
{
	$orderNum =  floor($info["id"] / pow(10, 9));
	
	if ($prevInfo == "" || getFamilyIDFromSpeciesID($prevInfo["id"]) != getFamilyIDFromSpeciesID($info["id"]))
	{
		$taxoInfo = getFamilyInfoFromSpeciesID($info["id"]); ?>
		<div class="subheading"><?= getFamilyDetailLinkFromSpeciesID($info["id"], "photo") ?></div>
<?	} ?>

    <div>
        <a href="./speciesdetail.php?view=photo&speciesid=<?= $info["id"] ?>">
             <?= $info["CommonName"] ?>
        </a>
        <? if ($info["photoCount"] > 1) echo "(" . $info["photoCount"] . ")"; ?>
        <? if (strtotime($info["latestPhoto"]) > $thresholdTime) echo "NEW"; ?>
	</div>

<?	$prevInfo = $info;
    $counter--;
    if ($counter == 0) { ?></td><td width="50%" class="rightcolumn"> <? }
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
