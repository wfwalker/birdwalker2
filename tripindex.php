
<?php

require("./birdwalker.php");

$tripListQuery = performQuery("select trip.*, date_format(Date, '%M %e') as niceDate, count(distinct sighting.SpeciesAbbreviation) as tripCount from trip, sighting where sighting.TripDate=trip.Date group by trip.Date order by trip.Date desc");
$tripListCount = mysql_num_rows($tripListQuery);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Trips</title>
  </head>

  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailTrips(); ?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle>Trip Lists</div>
        <div class=pagesubtitle> <?php echo $tripListCount ?> trips</div>
	  </div>


<table class=report-content>
<?php

$prevYear = "";
$counter = 0;
while($info = mysql_fetch_array($tripListQuery))
{
  $thisYear =  substr($info["Date"], 0, 4);
  if (strcmp($thisYear, $prevYear))
  {
    echo "<tr><td colspan=4 class=\"heading\">
            <a href=\"./yeardetail.php?year=" . $thisYear . "\">" . $thisYear . "</a></td></tr>";
  }

  if (($counter % 2) == 0) echo "\n<tr>";

  echo "<td class=firstcell><a href=\"./tripdetail.php?id=".$info["objectid"]."\">" . $info["Name"] . ", " . $info["niceDate"] . "</a></td>";
  echo "</td>";
  if (($counter % 2) == 1) echo "</tr>";

  $prevYear = $thisYear;
  $counter++;
}

?>

</table>

      </div>
    </div>
  </body>
</html>
