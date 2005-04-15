
<?

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./sightingquery.php");
require_once("./locationquery.php");
require_once("./tripquery.php");
require_once("./map.php");

$year = reqParam($_GET, "year");
$month = reqParam($_GET, "month");
$view = param($_GET, "view", "species");

$speciesQuery = new SpeciesQuery;
$speciesQuery->setFromRequest($_GET);

htmlHead(getMonthNameForNumber($month) . ", " . $year);

globalMenu();

if ($month == 1) { $prevMonth = 12; $prevYear = $year - 1; } else { $prevMonth = $month - 1; $prevYear = $year; }
if ($month == 12) { $nextMonth = 1; $nextYear = $year + 1; } else { $nextMonth = $month + 1; $nextYear = $year; }

$current = "year=" . $year . "&month=" . $month;
$next = "year=" . $nextYear . "&month=" . $nextMonth;
$prev = "year=" . $prevYear . "&month=" . $prevMonth;
$first = "year=" . getEarliestYear() . "&month=1";
$last = "year=" . getLatestYear() . "&month=12";

browseButtons("./monthdetail.php?view=" . $view . "&", $current, $first, $prev, $next, $last);

$items[] = "<a href=\"./tripindex.php#" . $year . "\">" . $year . "</a>";
navTrailTrips($items);
?>

    <div class=contentright>
      <div class="pagesubtitle">Month Detail</div>
      <div class="titleblock">	  
        <div class=pagetitle><?= getMonthNameForNumber($month) ?>, <a href="./yeardetail.php?year=<?= $year ?>"><?= $year ?></a></div>
          <div class=metadata>
              <a href="./monthdetail.php?view=trip&month=<?= $month ?>&year=<?= $year ?>">trip</a> |
              <a href="./monthdetail.php?view=species&month=<?= $month ?>&year=<?= $year ?>">species</a> |
              <a href="./monthdetail.php?view=map&month=<?= $month ?>&year=<?= $year ?>">map</a> | 
              <a href="./monthdetail.php?view=photo&month=<?= $month ?>&year=<?= $year ?>">photo</a><br/>
          </div>
		</div>

<?
if ($view == 'trip')
{
      $latestTrips = performQuery("
          SELECT *, date_format(Date, '%M %e') AS niceDate
              FROM trip WHERE Month(Date)=" . $month . " AND Year(Date)=" . $year . "
              ORDER BY Date DESC");

?>
	  <div class="heading">Trips</div>

<?    while ($info = mysql_fetch_array($latestTrips))
	  {
          $tripSpeciesCount = performCount("
              SELECT COUNT(DISTINCT(sighting.objectid))
                  FROM sighting
                  WHERE sighting.TripDate='" . $info["Date"] . "'"); ?>

          <div class="pagesubtitle"><?= $info["niceDate"] ?></div>

		  <div class="titleblock">
              <span class="heading">
                  <a href="./tripdetail.php?tripid=<?=$info["objectid"]?>">
<?                    rightThumbnail("SELECT * FROM sighting WHERE Photo='1' AND TripDate='" . $info["Date"] . "' LIMIT 1", false); ?>
                      <?= $info["Name"] ?>
                  </a>
              </span>
              <div class="subheading"><?= $tripSpeciesCount ?> species</div>
          </div>


          <div class=report-content><?= $info["Notes"] ?><br clear="all"/></div>
		  <p>&nbsp;</p>

<?	  }
}
else if ($view == "map")
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	$map = new Map("./yeardetail.php");
	$map->setFromRequest($_GET);
	$map->draw();
}
else if ($view == "species")
{
    $speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading($speciesQuery->getSpeciesCount(), "species");
	formatTwoColumnSpeciesList($speciesQuery);
}
elseif ($view == 'photo')
{
	$sightingQuery = new SightingQuery;
	$sightingQuery->setFromRequest($_GET);
	$sightingQuery->formatPhotos();
}

footer();
?>

    </div>

<?
htmlFoot();
?>