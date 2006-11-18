<?php

header("Content-Type: text/xml");

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$map = new Map("./" . $request->getPageScript(), $request);
$map->emitKML();

?>
