
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./tripquery.php");

$request = new Request;
$tripQuery = new TripQuery($request);

htmlHead("Trips");

$request->setView("trips");
$request->globalMenu();
?>

    <div class="topright-trip">
	  <div class="pagekind">index</div>
      <div class="pagetitle">Trips</div>
	</div>

    <div class="contentright">

<?
      $request->handleStandardViews();


footer();
?>

    </div>

<?
htmlFoot();
?>
