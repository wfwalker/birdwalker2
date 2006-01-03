
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

    <div class="topright">
	  <? browseButtons("<img align=\"center\" src=\"./images/trip.gif\"> Year Detail", "./yeardetail.php?view=" . $request->getView() . "&year=", $request->getYear(),
					 $prevYear, $prevYear, $nextYear, $nextYear); ?>

      <div class=pagetitle><?= $request->getYear() ?></div>
	</div>

    <div class="contentright">

      <div class="titleblock">	  
<?        $request->viewLinks("species"); ?>
	  </div>

<?
$request->handleStandardViews();
footer();
?>

    </div>

<?
htmlFoot();
?>
