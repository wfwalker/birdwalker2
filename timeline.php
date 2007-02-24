<?php header("Content-type: text/xml"); ?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"?>
<?

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./sightingquery.php");

$request = new Request;

$chrono = new ChronoList($request);
$chrono->timelineXML();
?>
