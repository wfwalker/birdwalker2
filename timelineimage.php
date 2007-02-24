<?
header("Content-type: image/gif");

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$chrono = new ChronoList($request);
$chrono->timelineGIF();
?>