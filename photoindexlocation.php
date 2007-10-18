<?php

require_once("./birdwalker.php");
require_once("./request.php");

$photoLocations = performQuery("Find Locations with Photos",
    "SELECT DISTINCT location.*, COUNT(DISTINCT sightings.id) AS photoCount
      FROM location, sighting
      WHERE locations.id=sightings.location_id AND sightings.photo='1'
      GROUP BY locations.id ORDER BY location.State, location.County, location.Name");

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
		if ($prevInfo == "" || ($prevInfo["state"] != $info["state"]) || ($prevInfo["county"] != $info["county"])) 
		{ ?>
          <div class="subheading"><?
		  if ($prevInfo == "" || $prevInfo["state"] != $info["state"])
		  { ?>
			  <span class="statename"><?= getStateNameForAbbreviation($info["state"]) ?></span>,
<?		  }

		  if ($prevInfo == "" || $prevInfo["county"] != $info["county"])
		  {
			  echo $info["county"] . " County";
		  } ?>
          </div>
<?      } ?>

        <div>
		   <a href="./locationdetail.php?view=photo&locationid=<?= $info["id"] ?>">
		   <?= $info["name"] ?></a> (<?= $info["photoCount"] ?>) <?= editlink("./locationcreate.php?locationid=" . $info["id"]) ?>
		   <div class="sighting-notes"><?= $info["notes"] ?></div>
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
