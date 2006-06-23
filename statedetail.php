
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

?>

    <div id="topright-location">
      <? stateBrowseButtons($request->getStateID(), $request->getView()); ?>
	  <div class=pagetitle><?= $info["Name"] ?></div>
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
