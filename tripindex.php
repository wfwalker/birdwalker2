
<?php

require("./birdwalker.php");

$tripListQuery = performQuery("
    SELECT trip.*, date_format(Date, '%M %e') AS niceDate, COUNT(DISTINCT sighting.SpeciesAbbreviation) AS tripCount
      FROM trip, sighting WHERE sighting.TripDate=trip.Date
      GROUP BY trip.Date
      ORDER BY trip.Date desc");

$tripListCount = mysql_num_rows($tripListQuery); ?>

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
?>

    <div class=contentright>
	  <div class="titleblock">
<?       rightThumbnailAll(); ?>
        <div class=pagetitle>Trips</div>
	  </div>

    <div class=heading> <?= $tripListCount ?> trips</div>


<? formatTwoColumnTripList($tripListQuery); ?>

      </div>
    </div>
  </body>
</html>
