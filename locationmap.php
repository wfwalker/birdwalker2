
<?php

require("./birdwalker.php");
require("./map.php");

$map = new Map("./locationmap.php");

$map->setFromRequest($_GET);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | OpenGIS</title>
  </head>
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
