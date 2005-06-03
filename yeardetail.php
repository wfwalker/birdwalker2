
<?

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./sightingquery.php");
require_once("./locationquery.php");
require_once("./tripquery.php");
require_once("./map.php");
require_once("./chronolist.php");

$request = new Request;

$request->getYear() == "" && die("Fatal error: missing year");

htmlHead($request->getYear());

globalMenu();
navTrailBirds();
?>

    <div class=contentright>
	<? browseButtons("Year Detail", "./yeardetail.php?view=" . $request->getView() . "&year=", $request->getYear(), getEarliestYear(), $request->getYear() - 1, $request->getYear() + 1, getLatestYear()); ?>

      <div class="titleblock">	  
<?    if ($request->getView() != "map")
         rightThumbnail("
            SELECT sighting.*, " . dailyRandomSeedColumn() . "
                FROM sighting
                WHERE sighting.Photo='1' AND Year(TripDate)='" . $request->getYear() . "'
                ORDER BY shuffle LIMIT 1", true); ?>
        <div class=pagetitle><?= $request->getYear() ?></div>
          <div class=metadata>
            locations:
			  <?= $request->linkToSelfChangeView("locations", "list") ?> |
			  <?= $request->linkToSelfChangeView("locationsbymonth", "by month") ?> |
			  <?= $request->linkToSelfChangeView("map", "map") ?><br/>
            species:	
			  <?= $request->linkToSelfChangeView("species", "list") ?> |
			  <?= $request->linkToSelfChangeView("chrono", "ABA") ?> |
			  <?= $request->linkToSelfChangeView("speciesbymonth", "by month") ?> |
			  <?= $request->linkToSelfChangeView("photo", "photo") ?><br/>
          </div>
		</div>

<?
$request->handleStandardViews("species");
footer();
?>

    </div>

<?
htmlFoot();
?>
