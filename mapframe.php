
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

htmlHead("Map");

$map = new Map("./" . $request->getPageScript(), $request);
$map->drawGoogle(true);

htmlFoot();
?>
