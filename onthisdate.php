<?php

require("./birdwalker.php");

$localtimearray = localtime(time(), 1);
$monthNum = $localtimearray["tm_mon"] + 1;
$dayStart = $localtimearray["tm_yday"] - 3;
$dayStop = $localtimearray["tm_yday"] + 3;

$tripsOnThisDate = performQuery("select * from trip where Month(Date)='" . ($localtimearray["tm_mon"] + 1) . "' and DayOfYear(Date)>='" . $dayStart . "' and DayOfYear(Date)<='" . $dayStop . "'");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | This Week in Birding History</title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class="contentright">
	  <div class=titleblock>
	    <div class=pagetitle>This Week in Birding History</div>
      </div>

<?php

	while($info = mysql_fetch_array($tripsOnThisDate))
	{
		echo "<a href=\"./tripdetail.php?id=". $info["objectid"] . "\">" . $info["Name"] . "</a> " . $info["Date"] . "<br/>";
	}
?>

    </div>
  </body>

</html>