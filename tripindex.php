
<?php

require("/Users/walker/Sites/birdwalker/birdwalker.php");

$tripListCount = getTripCount();
$tripListQuery = getTripQuery();

?>

<html>
  <head>
    <link title="Style" href="/~walker/birdwalker/stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Trips</title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle>Trip Lists</div>
        <div class=pagesubtitle> <?php echo $tripListCount ?> trips</div>
	  </div>


<table class=report-content>
<?php

$prevYear = "";
while($info = mysql_fetch_array($tripListQuery)) {
  $thisYear =  substr($info["Date"], 0, 4);
  if (strcmp($thisYear, $prevYear))
  {
    echo "<tr class=\"titleblock\"><td colspan=2>
            <a href=\"./yearindex.php?year=" . $thisYear . "\">" . $thisYear . "</a></td></tr>";
  }

  echo "<tr><td class=firstcell><a href=/~walker/birdwalker/tripdetail.php?id=".$info["objectid"].">" . $info["Name"] . " (" . $info["niceDate"] . ")</a></td><td align=right>" . $info["tripCount"] . "</td></tr>";
  $prevYear = $thisYear;
}

?>

</table>

      </div>
    </div>
  </body>
</html>
