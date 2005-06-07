
<?

require_once("./birdwalker.php");
require_once("./request.php");

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

$request->navTrailTrips();

?>

    <div class=contentright>
	  <? browseButtons("Month Detail", "./monthdetail.php?view=" . $request->getView() . "&", $current, $first, $prev, $next, $last); ?>
      <div class="titleblock">	  
        <div class=pagetitle><?= getMonthNameForNumber($request->getMonth()) ?>, <a href="./yeardetail.php?year=<?= $request->getYear() ?>"><?= $request->getYear() ?></a></div>


<?        $request->viewLinks(); ?>

		</div>

<?

$request->handleStandardViews("tripsummaries");
footer();

?>

    </div>

<?
htmlFoot();
?>