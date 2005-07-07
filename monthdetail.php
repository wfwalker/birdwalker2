
<?

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

htmlHead(getMonthNameForNumber($request->getMonth()) . ", " . $request->getYear());

globalMenu();

$concatenation = "concat(Year(tripdate), lpad(Month(tripdate), 2, '0'))";

$nextMonthInfo = performCount("
    SELECT min(" . $concatenation . ") FROM sighting WHERE " . $concatenation . " > '" . $request->getYear() . $request->getMonth() . "'");

$nextYear = substr($nextMonthInfo, 0, 4);
$nextMonth = substr($nextMonthInfo, 4, 2);

$prevMonthInfo = performCount("
    SELECT max(" . $concatenation . ") FROM sighting WHERE " . $concatenation . " < '" . $request->getYear() . $request->getMonth() . "'");

$prevYear = substr($prevMonthInfo, 0, 4);
$prevMonth = substr($prevMonthInfo, 4, 2);

$current = "year=" . $request->getYear() . "&month=" . $request->getMonth();
$next = "year=" . $nextYear . "&month=" . $nextMonth;
$prev = "year=" . $prevYear . "&month=" . $prevMonth;

$request->navTrailTrips();

?>

    <div class=contentright>
	  <? browseButtons("Month Detail", "./monthdetail.php?view=" . $request->getView() . "&", $current,
					   $prev, getMonthNameForNumber($prevMonth) . ", " . $prevYear,
					   $next, getMonthNameForNumber($nextMonth) . ", " . $nextYear); ?>
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