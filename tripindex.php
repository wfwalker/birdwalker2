
<?php

require_once("./birdwalker.php");
require_once("./tripquery.php");

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
	  <div class="pagesubtitle">Index</div>
	  <div class="titleblock">
<?       rightThumbnailAll(); ?>
        <div class=pagetitle>Trips</div>
	  </div>

	<div class=heading> <?= $tripQuery->getTripCount() ?> trips</div>


<? $tripQuery->formatTwoColumnTripList(); ?>

      </div>
<?
footer();
?>

    </div>

<?
htmlFoot();
?>
