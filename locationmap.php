
<?php

require_once("./birdwalker.php");
require_once("./map.php");

$map = new Map("./locationmap.php");

$map->setFromRequest($_GET);

htmlHead("OpenGIS");

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
