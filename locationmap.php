
<?php

require_once("./birdwalker.php");
require_once("./map.php");

$map = new Map("./locationmap.php");

$map->setFromRequest($_GET);

?>

<html>

  <? htmlHead("OpenGIS"); ?>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailLocations();
?>

    <div class=contentright>

	<? $map->draw();

    footer();
?>

   </div>

<?
   htmlFoot();
?>
