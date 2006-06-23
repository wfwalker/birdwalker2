
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./map.php");

$request = new Request;

$locationQuery = new LocationQuery($request);

htmlHead("Locations");

$request->globalMenu();
?>

    <div id="topright-location">
	  <div class="pagekind">index</div>
	  <div class="pagetitle">Locations</div>
      <? $request->viewLinks("locations"); ?>
	</div>

    <div id="contentright">
<?
	$request->handleStandardViews(); 

    footer();
?>

    </div>

<?
htmlFoot();
?>
