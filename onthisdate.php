<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./onthisdatespeciesquery.php");

$localtimearray = localtime(time(), 1);
$monthNum = $localtimearray["tm_mon"] + 1;
$dayNum = $localtimearray["tm_mday"];

$tripsOnThisDate = performQuery("Find Trips On This Date",
    "SELECT *, " . niceDateColumn() . "
      FROM trip
      WHERE Month(Date)='" . ($localtimearray["tm_mon"] + 1) . "' AND
        DayOfMonth(Date)='" . $dayNum . "'
        AND Year(Date)<" . getLatestYear() . "
      ORDER BY Date DESC");

$today = performCount("format date", "SELECT date_format(current_date, '%M %D')");

htmlHead($today);

$request = new Request;
$request->globalMenu();

?>

    <div class="topright-trip">
	    <div class="pagesubtitle">index</div>
	    <div class="pagetitle"><?= $today ?></div>
      </div>

    <div class="contentright">

<?

$onthisdatespeciesQuery = new OnThisDateSpeciesQuery($request);
doubleCountHeading($onthisdatespeciesQuery->getSpeciesCount(), "species", $onthisdatespeciesQuery->getPhotoCount(), "with photo");
$onthisdatespeciesQuery->formatTwoColumnSpeciesList();

      while ($info = mysql_fetch_array($tripsOnThisDate))
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
