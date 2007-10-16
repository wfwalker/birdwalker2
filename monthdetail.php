<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

htmlHead(getMonthNameForNumber($request->getMonth()) . ", " . ($request->getYear() == "" ? getEarliestYear() . " - " . getLatestYear() :  $request->getYear()));

$concatenation = "concat(Year(trip.Date), lpad(Month(trip.Date), 2, '0'))";

$request->globalMenu();

?>

    <div id="topright-trip">

<?
if ($request->getYear() != "")
{
	$nextMonthInfo = performCount("Find Next Month",
    "SELECT min(" . $concatenation . ") FROM sighting,trip WHERE sighting.trip_id=trip.id AND " . $concatenation . " > '" . $request->getYear() . $request->getMonth() . "'");
	
	$nextYear = substr($nextMonthInfo, 0, 4);
	$nextMonth = substr($nextMonthInfo, 4, 2);
	
	$prevMonthInfo = performCount("Find Previous Month",
    "SELECT max(" . $concatenation . ") FROM sighting,trip WHERE sighting.trip_id=trip.id AND " . $concatenation . " < '" . $request->getYear() . $request->getMonth() . "'");
	
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
	<div class="pagetitle"><?= getMonthNameForNumber($request->getMonth()) ?> <?= $request->getYear() == "" ? getEarliestYear() . " - " . getLatestYear() :  $request->getYear() ?></div>
	</div>

    <div id="contentright">
      <table width="100%">
        <tr valign="top">
          <td width="50%" class="leftcolumn">
            <div class="subheading">Where we went</div>
            <p/>
<?            $mapRequest = new Request;
              $mapRequest->setMapWidth(300);
              $mapRequest->setMapHeight(300);
              $map = new Map("./" . $mapRequest->getPageScript(), $mapRequest);

              $map->draw(false); ?>
          </td>
        <td  width="50%" class="rightcolumn">
          <div class="subheading">When we went</div>
          <p/>
          <div class="report-content">
<?	      $tripQuery = new TripQuery($request);
		  $tripQuery->formatSummaries(); ?>
          </div>
        </td>
      </tr>
    </table>

    <div style="border-top: solid 1px #AAAAAA; margin-left: 9px; margin-right: 9px"/>

    <div class="report-content">
<?    $speciesQuery = new SpeciesQuery($request);
      $speciesQuery->formatTwoColumnSpeciesList(); ?>
    </div>

<?  footer(); ?>

  </div>

<?
htmlFoot();
?>
