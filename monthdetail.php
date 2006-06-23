
<?

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

htmlHead(getMonthNameForNumber($request->getMonth()) . ", " . ($request->getYear() == "" ? getEarliestYear() . " - " . getLatestYear() :  $request->getYear()));

$concatenation = "concat(Year(tripdate), lpad(Month(tripdate), 2, '0'))";

$request->globalMenu();

?>

    <div class="topright-trip">

<?
if ($request->getYear() != "")
{
	$nextMonthInfo = performCount("Find Next Month",
    "SELECT min(" . $concatenation . ") FROM sighting WHERE " . $concatenation . " > '" . $request->getYear() . $request->getMonth() . "'");
	
	$nextYear = substr($nextMonthInfo, 0, 4);
	$nextMonth = substr($nextMonthInfo, 4, 2);
	
	$prevMonthInfo = performCount("Find Previous Month",
    "SELECT max(" . $concatenation . ") FROM sighting WHERE " . $concatenation . " < '" . $request->getYear() . $request->getMonth() . "'");
	
	$prevYear = substr($prevMonthInfo, 0, 4);
	$prevMonth = substr($prevMonthInfo, 4, 2);
	
	$current = "year=" . $request->getYear() . "&month=" . $request->getMonth();
	$next = "year=" . $nextYear . "&month=" . $nextMonth;
	$prev = "year=" . $prevYear . "&month=" . $prevMonth;

	browseButtons("Month Detail", "./monthdetail.php?view=" . $request->getView() . "&", $current,
				  $prev, getMonthNameForNumber($prevMonth) . ", " . $prevYear,
				  $next, getMonthNameForNumber($nextMonth) . ", " . $nextYear);
}
else
{
	$current = $request->getMonth();
	$nextMonth = $current == 12 ? 1 : $current + 1;
	$prevMonth = $current == 1 ? 12 : $current - 1;
	$next = "month=" . $nextMonth;
	$prev = "month=" . $prevMonth;

	browseButtons("Month Detail", "./monthdetail.php?view=" . $request->getView() . "&", $current,
				  $prev, getMonthNameForNumber($prevMonth),
				  $next, getMonthNameForNumber($nextMonth));
}
 ?>
	<div class=pagetitle><?= getMonthNameForNumber($request->getMonth()) ?> <?= $request->getYear() == "" ? getEarliestYear() . " - " . getLatestYear() :  $request->getYear() ?></div>

<?        $request->viewLinks("tripsummaries"); ?>
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