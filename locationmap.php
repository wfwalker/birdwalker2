
<?php

require("./birdwalker.php");
require("./map.php");

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

	<? $map->draw(); ?>

   </div>

  </body>
</html>
