
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./chronolist.php");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);

htmlHead("Species");
$request->globalMenu();

?>

    <div id="topright-species">
	  <div class="pagekind">index</div>
	  <div class="pagetitle">Species</div>
      <? $request->viewLinks("species"); ?>
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
