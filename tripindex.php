
<?php

require("./birdwalker.php");
require("./tripquery.php");

$tripQuery = new TripQuery;
?>

<html>

  <? htmlHead("Trips"); ?>

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
