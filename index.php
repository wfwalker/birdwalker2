
<?php

require_once("./birdwalker.php");

$numberOfTrips = 10;
$latestTrips = performQuery("select *, date_format(Date, '%M %e, %Y') AS niceDate from trip order by Date desc LIMIT " . $numberOfTrips);
$randomPhotoSightings = performQuery("SELECT *, rand(" . $randomNumberSeed . ") AS shuffle FROM sighting WHERE Photo='1' ORDER BY shuffle LIMIT 5");

htmlHead("Home");

globalMenu();
disabledBrowseButtons();
?>

    <div class=contentright>

	  <p>Welcome to <code>birdWalker</code>! This website contains Bill and Mary&#39;s birding field notes, including
	  trip, county, state, and year lists. Our latest trips are listed below, other indices
	  are available from the links on the left.</p>

	  <div class="heading">Today&#39;s Featured Photos</div>

      <table width="100%"><tr>

<?    for ($index = 0; $index < 5; $index++)
	  {
		  $info = mysql_fetch_array($randomPhotoSightings); ?>
		  <td align=right><?= getThumbForSightingInfo($info) ?></td>
<?	  } ?>

      </tr></table>

	  <p>&nbsp;</p>

	  <div class="heading">Latest Trips</div>

<?    for ($index = 0; $index < $numberOfTrips; $index++)
	  {
		  $info = mysql_fetch_array($latestTrips);
          $tripSpeciesCount = performCount("SELECT COUNT(DISTINCT(sighting.objectid)) from sighting where sighting.TripDate='" . $info["Date"] . "'"); ?>

          <div class="pagesubtitle"><?= $info["niceDate"] ?></div>

		  <div class="titleblock">
              <span class="heading">
                  <a href="./tripdetail.php?tripid=<?=$info["objectid"]?>">
<?                    rightThumbnail("SELECT * FROM sighting WHERE Photo='1' AND TripDate='" . $info["Date"] . "' LIMIT 1", false); ?>
                      <?= $info["Name"] ?>
                  </a>
              </span>
              <div class="subheading"><?= $tripSpeciesCount ?> species</div>
          </div>


          <div class=report-content><?= $info["Notes"] ?><br clear="all"/></div>
		  <p>&nbsp;</p>

<?	  }

      footer();
?>
    </div>

<?
htmlFoot();
?>
