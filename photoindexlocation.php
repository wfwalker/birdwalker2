
<?php

require_once("./birdwalker.php");

$photoLocations = performQuery("
    SELECT DISTINCT location.*, COUNT(DISTINCT sighting.objectid) AS photoCount
      FROM location, sighting
      WHERE location.Name=sighting.LocationName AND sighting.Photo='1'
      GROUP BY location.objectid ORDER BY location.State, location.County, location.Name");
$photoCount = performCount("
    SELECT COUNT(*) FROM sighting WHERE Photo='1'");

?>

<html>

  <? htmlHead("Photo List"); ?>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailPhotos();
?>

    <div class=contentright>
      <div class="titleblock">
<?      rightThumbnailAll(); ?>
	    <div class=pagetitle>Photo Index</div>
        <div class=metadata>
          <a href="./photoindextaxo.php">by species<a/> |
          <a href="./photoindex.php">by date</a> |
          <a href="./photoindexlocation.php">by location</a>
        </div>
      </div>

   <div class=heading><?= $photoCount ?> photos made at <?= mysql_num_rows($photoLocations) ?> locations</div>

<table width="100%">
<tr valign=top><td width="50%" class=report-content>

<?  while($info = mysql_fetch_array($photoLocations))
    { ?>
        <div><a href="./locationdetail.php?view=photo&id=<?= $info["objectid"] ?>"><?= $info["Name"] ?></a> (<?= $info["photoCount"] ?>)</div>
<?  } ?>

    </div>
  </body>
</html>
