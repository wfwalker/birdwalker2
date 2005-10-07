
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

    <div class="topright">
	  <div class="pagesubtitle">index</div>
	  <div class="pagetitle">Species</div>
	</div>

    <div class="contentright">

      <div class="titleblock">	  
        <? $request->viewLinks("species"); ?>
      </div>

<?

$request->handleStandardViews();

footer();

?>

    </div>

<?
htmlFoot();
?>
