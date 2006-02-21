
<?

require_once("./request.php");

$request = new Request;

$request->getYear() == "" && die("Fatal error: missing year");

htmlHead($request->getYear());

$prevYear = performCount("Get Previous Year",
    "SELECT MAX(Year(TripDate)) FROM sighting WHERE Year(TripDate) < " . $request->getYear());

$nextYear = performCount("Get Next Year",
    "SELECT MIN(Year(TripDate)) FROM sighting WHERE Year(TripDate) > " . $request->getYear());

$request->globalMenu();

?>

    <div class="topright-trip">
	  <? browseButtons("Year Detail", "./yeardetail.php?view=" . $request->getView() . "&year=", $request->getYear(),
					 $prevYear, $prevYear, $nextYear, $nextYear); ?>

      <div class=pagetitle><?= $request->getYear() ?></div>
<?    $request->viewLinks("species"); ?>
	</div>

    <div class="contentright">

<?
$request->handleStandardViews();
footer();
?>

    </div>

<?
htmlFoot();
?>
