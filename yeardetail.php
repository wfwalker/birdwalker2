
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

$year = reqParam($_GET, "year");
$view = param($_GET, "view", "species");

htmlHead($year);

globalMenu();
navTrailBirds();
?>

    <div class=contentright>
	<? browseButtons("Year Detail", "./yeardetail.php?view=" . $view . "&year=", $year, getEarliestYear(), $year - 1, $year + 1, getLatestYear()); ?>
      <div class="titleblock">	  
<?    if ($view != "map")
         rightThumbnail("
            SELECT sighting.*, " . dailyRandomSeedColumn() . "
                FROM sighting
                WHERE sighting.Photo='1' AND Year(TripDate)='" . $year . "'
                ORDER BY shuffle LIMIT 1", true); ?>
        <div class=pagetitle><?= $year ?></div>
          <div class=metadata>
            locations:
              <a href="./yeardetail.php?view=locations&year=<?= $year ?>">list</a> |
	          <a href="./yeardetail.php?view=locationsbymonth&year=<?= $year ?>">by month</a> |
              <a href="./yeardetail.php?view=map&year=<?= $year ?>">map</a> <br/>
            species:	
              <a href="./yeardetail.php?view=species&year=<?= $year ?>">list</a> |
	          <a href="./yeardetail.php?view=chrono&year=<?= $year ?>">ABA</a> |
	          <a href="./yeardetail.php?view=speciesbymonth&year=<?= $year ?>">by month</a> |
              <a href="./yeardetail.php?view=species&view=photo&year=<?= $year ?>">photo</a><br/>
          </div>
		</div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery($request);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 

	$tripQuery = new TripQuery($request);
	countHeading( $tripQuery->getTripCount(), "trip");
	$tripQuery->formatTwoColumnTripList();
}
elseif ($view == 'speciesbyyear')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByYearTable(); 
}
elseif ($view == 'speciesbymonth')
{
	$speciesQuery = new SpeciesQuery($request);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery($request);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatTwoColumnLocationList("species", true);

	$tripQuery = new TripQuery($request);
	countHeading( $tripQuery->getTripCount(), "trip");
	$tripQuery->formatTwoColumnTripList();
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery($request);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByYearTable();
}
elseif ($view == 'locationsbymonth')
{
    $locationQuery = new LocationQuery($request);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByMonthTable();
}
else if ($view == "map")
{
	$map = new Map("./yeardetail.php", $request);
	$map->draw();
}
else if ($view == "chrono")
{
	$chrono = new ChronoList($request);
	$chrono->draw();
}
elseif ($view == 'photo')
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
