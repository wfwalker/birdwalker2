
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./map.php");

$request = new Request;

$locationQuery = new LocationQuery($request);

htmlHead("Locations");

$request->globalMenu();
?>

    <div class="topright">
	  <div class="pagesubtitle">index</div>
	  <div class="pagetitle">Locations</div>
	</div>

    <div class="contentright">
      <div class="titleblock">
	    <? $request->viewLinks("locations"); ?>
	  </div>

<?
	$request->handleStandardViews(); 

    footer();
?>

    </div>

<?
htmlFoot();
?>
