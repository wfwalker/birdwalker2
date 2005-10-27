<?php

require_once("./birdwalker.php");
require_once("./request.php");

$localtimearray = localtime(time(), 1);
$monthNum = $localtimearray["tm_mon"] + 1;
$dayStart = $localtimearray["tm_yday"] - 3;
$dayStop = $localtimearray["tm_yday"] + 3;

$tripsOnThisDate = performQuery("Find Trips On This Date",
    "SELECT *, " . niceDateColumn() . "
      FROM trip
      WHERE Month(Date)='" . ($localtimearray["tm_mon"] + 1) . "' AND
        DayOfYear(Date)>='" . $dayStart . "' AND DayOfYear(Date)<='" . $dayStop . "'
        AND Year(Date)<" . getLatestYear() . "
      ORDER BY Date DESC");

htmlHead("This Week in Birding History");

$request = new Request;
$request->globalMenu();
?>

    <div class="topright">
	    <div class="pagesubtitle">index</div>
	    <div class="pagetitle">This Week in Birding History</div>
      </div>

    <div class="contentright">

<?    while ($info = mysql_fetch_array($tripsOnThisDate))
      {
          $tripSpeciesCount = performCount("Count Species For This Trip",
		      "SELECT COUNT(DISTINCT(sighting.objectid)) from sighting where sighting.TripDate='" . $info["Date"] . "'"); ?>

		  <p>&nbsp;</p>

          <div class="superheading"><?= $info["niceDate"] ?></div>

		  <div class="summaryblock">
              <span class="heading">
                  <a href="./tripdetail.php?tripid=<?=$info["objectid"]?>">
<?                    rightThumbnail("SELECT * FROM sighting WHERE Photo='1' AND TripDate='" . $info["Date"] . "' LIMIT 1", false); ?>
                      <?= $info["Name"] ?>
                  </a>
              </span>
              <div class="subheading"><?= $tripSpeciesCount ?> species</div>
          </div>

          <div class=report-content><?= $info["Notes"] ?><br clear="all"/></div>
<?	  }

footer();
?>

    </div>

<?
htmlFoot();
?>
