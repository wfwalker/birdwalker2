
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$request = new Request;

$info = $request->getStateInfo();

htmlHead($info["Name"]);

$request->globalMenu();
//$request->navTrailLocations();

?>

    <div class="topright">
      <? stateBrowseButtons($request->getStateID(), $request->getView()); ?>
	  <div class=pagetitle><?= $info["Name"] ?></div>
	</div>

    <div class="contentright">
      <div class="titleblock">	  
<?    if ($request->getView() != "map") rightThumbnailState($info["Abbreviation"]); ?>

<?    $request->viewLinks("species"); ?>

    </div>

<?

$request->handleStandardViews();
footer();

?>

    </div>

<?
htmlFoot();
?>
