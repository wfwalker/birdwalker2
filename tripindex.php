
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

<?php
globalMenu();
disabledBrowseButtons();
navTrailTrips("");
pageThumbnail("select *, rand() as shuffle from sighting where Photo='1' order by shuffle");
?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle>Trips</div>
        <div class=pagesubtitle> <?= $tripListCount ?> trips</div>
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
?>
    <tr><td colspan=4 class="heading"><a name="<?= $thisYear ?>"></a><?= $thisYear ?></td></tr>
<?
  }

  if (($counter % 2) == 0) { ?><tr><? } ?>

  <td class=firstcell><a href="./tripdetail.php?id=<?= $info["objectid"] ?>"><?= $info["Name"] ?>, <?= $info["niceDate"] ?></a></td>
  </td>

<?
  if (($counter % 2) == 1) { ?></tr><? }

  $prevYear = $thisYear;
  $counter++;
}

?>

</table>

      </div>
    </div>
  </body>
</html>
