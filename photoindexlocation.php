
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$photoLocations = performQuery("Find Locations with Photos",
    "SELECT DISTINCT location.*, COUNT(DISTINCT sighting.objectid) AS photoCount
      FROM location, sighting
      WHERE location.Name=sighting.LocationName AND sighting.Photo='1'
      GROUP BY location.objectid ORDER BY location.State, location.County, location.Name");

$photoCount = performCount("Count Photos",
    "SELECT COUNT(*) FROM sighting WHERE Photo='1'");


htmlHead("Photo List");

$request = new Request;
$request->globalMenu();
?>

  <div class="topright-location">
	  <div class="pagesubtitle">Index</div>
	  <div class="pagetitle">Photos</div>
  </div>

    <div class="contentright">
      <div class="titleblock">
        <div class="metadata">
          <a href="./photoindextaxo.php">by species<a/> |
          <a href="./photoindex.php">by date</a> |
          <a href="./photoindexlocation.php">by location</a>
        </div>
      </div>

   <div class="heading"><?= $photoCount ?> photos made at <?= mysql_num_rows($photoLocations) ?> locations</div>

<table width="100%">
<tr valign=top><td width="50%" class="report-content">

<?
	$prevInfo = "";
	while($info = mysql_fetch_array($photoLocations))
    {
		if ($prevInfo == "" || ($prevInfo["State"] != $info["State"]) || ($prevInfo["County"] != $info["County"])) 
		{ ?>
          <div class=subheading><?
		  if ($prevInfo == "" || $prevInfo["State"] != $info["State"]) { echo getStateNameForAbbreviation($info["State"]) . ", "; }
		  if ($prevInfo == "" || $prevInfo["County"] != $info["County"]) { echo $info["County"] . " County"; } ?>
          </div>
<?      } ?>

        <div><a href="./locationdetail.php?view=photo&locationid=<?= $info["objectid"] ?>"><?= $info["Name"] ?></a> (<?= $info["photoCount"] ?>)</div>
<?      $prevInfo = $info;
    }

footer();
?>

    </div>

<?
htmlFoot();
?>
