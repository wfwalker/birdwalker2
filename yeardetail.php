
<?

require_once("./request.php");

$request = new Request;

$request->getYear() == "" && die("Fatal error: missing year");

htmlHead($request->getYear());

globalMenu();
navTrail();
?>

    <div class=contentright>
	<? browseButtons("Year Detail", "./yeardetail.php?view=" . $request->getView() . "&year=", $request->getYear(), getEarliestYear(), $request->getYear() - 1, $request->getYear() + 1, getLatestYear()); ?>

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
