
<?php

require_once("./birdwalker.php");
require_once("./tripquery.php");

$tripQuery = new TripQuery;

htmlHead("Trips");

globalMenu();
navTrailTrips("");
?>

    <div class=contentright>
	  <div class="pagesubtitle">Index</div>
	  <div class="titleblock">
<?       rightThumbnailAll(); ?>
        <div class=pagetitle>Trips</div>
	  </div>

	  <div class=heading> <?= $tripQuery->getTripCount() ?> trips</div>


<?
      $tripQuery->formatTwoColumnTripList();


footer();
?>

    </div>

<?
htmlFoot();
?>
