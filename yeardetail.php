<?

require_once("./request.php");

$request = new Request;

$request->getYear() == "" && die("Fatal error: missing year");

htmlHead($request->getYear());

$prevYear = performCount("Get Previous Year",
    "SELECT MAX(Year(trips.Date)) FROM sightings, trips WHERE trips.id=sightings.trip_id AND Year(trips.Date) < " . $request->getYear());

$nextYear = performCount("Get Next Year",
    "SELECT MIN(Year(trips.Date)) FROM sightings, trips WHERE trips.id=sightings.trip_id AND Year(trips.Date) > " . $request->getYear());

$request->globalMenu();

?>

    <div id="topright-trip">
	  <? browseButtons("Year Detail", "./yeardetail.php?view=" . $request->getView() . "&year=", $request->getYear(),
					 $prevYear, $prevYear, $nextYear, $nextYear); ?>

      <div class="pagetitle"><?= $request->getYear() ?></div>
<?    $request->viewLinks("species"); ?>
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
