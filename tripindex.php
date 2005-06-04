
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./tripquery.php");

$request = new Request;
$tripQuery = new TripQuery($request);

htmlHead("Trips");

globalMenu();
navTrail("");
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
