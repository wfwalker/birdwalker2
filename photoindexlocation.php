
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

  <div id="topright-location">
	  <div class="pagekind">Index</div>
	  <div class="pagetitle">Photos</div>
      <div class="pagesubtitle">
      <a href="./photoindextaxo.php">by species</a> |
      <a href="./photoindex.php">by date</a> |
      <a href="./photoindexlocation.php">by location</a>
      </div>
  </div>

    <div id="contentright">
      <div class="heading"><?= $photoCount ?> photos made at <?= mysql_num_rows($photoLocations) ?> locations</div>

      <div class="onecolumn">
<?
	$prevInfo = "";
	while($info = mysql_fetch_array($photoLocations))
    {
		if ($prevInfo == "" || ($prevInfo["State"] != $info["State"]) || ($prevInfo["County"] != $info["County"])) 
		{ ?>
          <div class="subheading"><?
		  if ($prevInfo == "" || $prevInfo["State"] != $info["State"])
		  { ?>
			  <span class="statename"><?= getStateNameForAbbreviation($info["State"]) ?></span>,
<?		  }

		  if ($prevInfo == "" || $prevInfo["County"] != $info["County"])
		  {
			  echo $info["County"] . " County";
		  } ?>
          </div>
<?      } ?>

        <div>
		   <a href="./locationdetail.php?view=photo&locationid=<?= $info["objectid"] ?>">
		   <?= $info["Name"] ?></a> (<?= $info["photoCount"] ?>) <?= editlink("./locationcreate.php?locationid=" . $info["objectid"]) ?>
		   <div class="sighting-notes"><?= $info["Notes"] ?></div>
	    </div>
<?      $prevInfo = $info;
    }

?>   </div> <?

footer();
?>

    </div>

<?
htmlFoot();
?>
