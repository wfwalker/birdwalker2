
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./tripquery.php");

$request = new Request;
$tripQuery = new TripQuery($request);

htmlHead("Trips");

$request->globalMenu();
?>

    <div class="topright">
	  <div class="pagesubtitle">index</div>
      <div class="pagetitle">Trips</div>
	</div>

    <div class="contentright">
	  <div class="titleblock">
<?       rightThumbnailAll(); ?>
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
