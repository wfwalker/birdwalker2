<?php

require("./birdwalker.php");

$localtimearray = localtime(time(), 1);
$monthNum = $localtimearray["tm_mon"] + 1;
$dayStart = $localtimearray["tm_yday"] - 3;
$dayStop = $localtimearray["tm_yday"] + 3;

$tripsOnThisDate = performQuery("
    SELECT *, date_format(Date, '%M %e, %Y') AS niceDate
      FROM trip
      WHERE Month(Date)='" . ($localtimearray["tm_mon"] + 1) . "' AND
        DayOfYear(Date)>='" . $dayStart . "' AND DayOfYear(Date)<='" . $dayStop . "'
        AND Year(Date)<2004
      ORDER BY Date DESC"); ?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | This Week in Birding History</title>
  </head>

  <body>

<?php
globalMenu();
$items[] = "";
navTrail($items);
disabledBrowseButtons();
?>

    <div class="contentright">
	  <div class=titleblock>
	    <div class=pagetitle>This Week in Birding History</div>
      </div>

<?    while ($info = mysql_fetch_array($tripsOnThisDate))
      { ?>
		  <p>&nbsp;</p>

          <div class="pagesubtitle"><?= $info["niceDate"] ?></div>

		  <div class="titleblock">
<?         rightThumbnail("SELECT * FROM sighting WHERE Photo='1' AND TripDate='" . $info["Date"] . "' LIMIT 1"); ?>
              <span class="pagetitle"><a href="./tripdetail.php?id=<?=$info["objectid"]?>"/><?= $info["Name"] ?></a></span>
          </div>

          <div class=report-content><?= $info["Notes"] ?><br clear="all"/></div>
<?	  } ?>

    </div>
  </body>

</html>