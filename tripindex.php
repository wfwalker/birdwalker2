
<?php

require("./birdwalker.php");
require("./tripquery.php");

$tripQuery = new TripQuery;
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
?>

    <div class=contentright>
	  <div class="titleblock">
<?       rightThumbnailAll(); ?>
        <div class=pagetitle>Trips</div>
	  </div>

	<div class=heading> <?= $tripQuery->getTripCount() ?> trips</div>


<? $tripQuery->formatTwoColumnTripList(); ?>

      </div>
    </div>
  </body>
</html>
