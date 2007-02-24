<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$request->getCounty() == "" && die("Fatal error: missing county");

htmlHead($request->getCounty() . " County");

$request->globalMenu();

$locationQuery = new LocationQuery($request);
$stateInfo =  $request->getStateInfo();

?>

    <div id="topright-location">
	  <div class="pagekind">County Detail</div>
	  <div class="pagetitle"> <?= $request->getCounty() ?> County</div>
	  <div class="pagesubtitle"> <?= $stateInfo["Name"] ?></div>
<?    $request->viewLinks("species"); ?>
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
