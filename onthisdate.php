<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");

$request = new Request;

$request->isDayOfMonthSpecified() || die("Fatal error: Missing day of month");
$request->isMonthSpecified() || die("Fatal error: Missing month");

$request->globalMenu();

$tripQuery = new TripQuery($request);
$speciesQuery = new SpeciesQuery($request);

htmlHead(getMonthNameForNumber($request->getMonth()) . " " . $request->getDayOfMonth());

?>
    <div id="topright-trip">
  <? /*  browseButtons("On This Date", "./onthisdate.php?month=" . $request->getMonth() . "&dayofmonth=", $request->getDayOfMonth(), $request->getDayOfMonth() - 1, $request->getDayOfMonth() - 1, $request->getDayOfMonth() + 1, $request->getDayOfMonth() + 1); */ ?>
	    <div class="pagesubtitle">index</div>
        <div class="pagetitle"><?= $request->getPageTitle() ?></div>
      </div>

    <div id="contentright">

    <table>
      <tr valign="top">
        <td class="leftcolumn" width="300px">
            <div class="subheading">Where we went</div>
            <p/>
<?            $mapRequest = new Request;
              $mapRequest->setMapWidth(300);
              $mapRequest->setMapHeight(300);
              $map = new Map("./" . $mapRequest->getPageScript(), $mapRequest);

              $map->draw(false); ?>
        </td>

        <td class="rightcolumn" width="300px">
          <div class="subheading">When we went</div>
          <p/>
<?        $tripQuery->formatSummaries(); ?>
        </td>
      </tr>
    </table>

    <div style="border-top: solid 1px #AAAAAA; margin-left: 9px; margin-right: 9px"/>

    <div class="report-content">

<?    $speciesQuery->formatTwoColumnSpeciesList(); ?>
    </div>
<?
    footer();
?>
  </div>

<?
htmlFoot();
?>
