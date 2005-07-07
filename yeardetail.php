
<?

require_once("./request.php");

$request = new Request;

$request->getYear() == "" && die("Fatal error: missing year");

htmlHead($request->getYear());

$prevYear = performCount("SELECT MAX(Year(TripDate)) FROM sighting WHERE Year(TripDate) < " . $request->getYear());
$nextYear = performCount("SELECT MIN(Year(TripDate)) FROM sighting WHERE Year(TripDate) > " . $request->getYear());

globalMenu();
$request->navTrailTrips();
?>

    <div class=contentright>
	<? browseButtons("Year Detail", "./yeardetail.php?view=" . $request->getView() . "&year=", $request->getYear(),
					 $prevYear, $prevYear, $nextYear, $nextYear); ?>

      <div class="titleblock">	  
<?    if ($request->getView() != "map" && $request->getView() != "photo")
         rightThumbnail("
            SELECT sighting.*, " . dailyRandomSeedColumn() . "
                FROM sighting
                WHERE sighting.Photo='1' AND Year(TripDate)='" . $request->getYear() . "'
                ORDER BY shuffle LIMIT 1", true); ?>
        <div class=pagetitle><?= $request->getYear() ?></div>

<?        $request->viewLinks(); ?>

		</div>

<?
$request->handleStandardViews("species");
footer();
?>

    </div>

<?
htmlFoot();
?>
