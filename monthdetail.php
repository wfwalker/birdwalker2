
<?

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");
require_once("./sightingquery.php");
require_once("./locationquery.php");
require_once("./tripquery.php");
require_once("./map.php");

$request = new Request;

htmlHead(getMonthNameForNumber($request->getMonth()) . ", " . $request->getYear());

globalMenu();

if ($request->getMonth() == 1) { $prevMonth = 12; $prevYear = $request->getYear() - 1; } else { $prevMonth = $request->getMonth() - 1; $prevYear = $request->getYear(); }
if ($request->getMonth() == 12) { $nextMonth = 1; $nextYear = $request->getYear() + 1; } else { $nextMonth = $request->getMonth() + 1; $nextYear = $request->getYear(); }

$current = "year=" . $request->getYear() . "&month=" . $request->getMonth();
$next = "year=" . $nextYear . "&month=" . $nextMonth;
$prev = "year=" . $prevYear . "&month=" . $prevMonth;
$first = "year=" . getEarliestYear() . "&month=1";
$last = "year=" . getLatestYear() . "&month=12";



$items[] = "<a href=\"./tripindex.php#" . $request->getYear() . "\">" . $request->getYear() . "</a>";
navTrailTrips($items);
?>

    <div class=contentright>
	  <? browseButtons("Month Detail", "./monthdetail.php?view=" . $request->getView() . "&", $current, $first, $prev, $next, $last); ?>
      <div class="titleblock">	  
        <div class=pagetitle><?= getMonthNameForNumber($request->getMonth()) ?>, <a href="./yeardetail.php?year=<?= $request->getYear() ?>"><?= $request->getYear() ?></a></div>
          <div class=metadata>
	          <?= $request->linkToSelfChangeView("trip", "trip") ?> |
	          <?= $request->linkToSelfChangeView("species", "species") ?> |
	          <?= $request->linkToSelfChangeView("map", "map") ?> |
	          <?= $request->linkToSelfChangeView("photo", "photo") ?><br/>
          </div>
		</div>

<?
if ($request->getView() == 'trip')
{
	$tripQuery = new TripQuery($request);
	$tripQuery->formatSummaries();
}
else if ($request->getView() == "map")
{
	$map = new Map("./monthdetail.php", $request);
	$map->draw();
}
else if ($request->getView() == "species")
{
    $speciesQuery = new SpeciesQuery($request);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	formatTwoColumnSpeciesList($speciesQuery);
}
elseif ($request->getView() == 'photo')
{
	$sightingQuery = new SightingQuery($request);
	$sightingQuery->formatPhotos();
}

footer();
?>

    </div>

<?
htmlFoot();
?>