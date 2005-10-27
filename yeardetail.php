
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
	  <? browseButtons("Year Detail", "./yeardetail.php?view=" . $request->getView() . "&year=", $request->getYear(),
					 $prevYear, $prevYear, $nextYear, $nextYear); ?>

      <div class=pagetitle><?= $request->getYear() ?></div>
	</div>

    <div class="contentright">

      <div class="titleblock">	  
<?    if ($request->getView() != "map" && $request->getView() != "photo")
         rightThumbnail("
            SELECT sighting.*, " . dailyRandomSeedColumn() . "
                FROM sighting
                WHERE sighting.Photo='1' AND Year(TripDate)='" . $request->getYear() . "'
                ORDER BY shuffle LIMIT 1", true); ?>

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
