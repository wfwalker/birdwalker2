
<?

require_once("./birdwalker.php");
require_once("./speciesquery.php");
require_once("./sightingquery.php");
require_once("./locationquery.php");
require_once("./tripquery.php");
require_once("./map.php");

$year = param($_GET, "year", "1998");
$view = param($_GET, "view", "species");

$speciesQuery = new SpeciesQuery;
$speciesQuery->setFromRequest($_GET);

?>

<html>

  <? htmlHead($year) ?>

  <body>

<?
globalMenu();
browseButtons("./yeardetail.php?view=" . $view . "&year=", $year, getEarliestYear(), $year - 1, $year + 1, getLatestYear());
navTrailBirds();
?>

    <div class=contentright>
      <div class="pagesubtitle">Year Detail</div>
      <div class="titleblock">	  
<?    if ($view != "map")
         rightThumbnail("
            SELECT sighting.*, rand() AS shuffle
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
              <a href="./chronocayearlist.php?year=<?= $year ?>">ABA</a> |
	          <a href="./yeardetail.php?view=speciesbymonth&year=<?= $year ?>">by month</a> |
              <a href="./yeardetail.php?view=species&view=photo&year=<?= $year ?>">photo</a><br/>
          </div>
		</div>

<?
if ($view == 'species')
{
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatTwoColumnSpeciesList(); 

	$tripQuery = new TripQuery;
	$tripQuery->setFromRequest($_GET);
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
	$speciesQuery = new SpeciesQuery;
	$speciesQuery->setFromRequest($_GET);
	countHeading( $speciesQuery->getSpeciesCount(), "species");
	$speciesQuery->formatSpeciesByMonthTable(); 
}
elseif ($view == 'locations')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatTwoColumnLocationList();

	$tripQuery = new TripQuery;
	$tripQuery->setFromRequest($_GET);
	countHeading( $tripQuery->getTripCount(), "trip");
	$tripQuery->formatTwoColumnTripList();
}
elseif ($view == 'locationsbyyear')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByYearTable();
}
elseif ($view == 'locationsbymonth')
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	countHeading( $locationQuery->getLocationCount(), "location");
	$locationQuery->formatLocationByMonthTable();
}
else if ($view == "map")
{
    $locationQuery = new LocationQuery;
	$locationQuery->setFromRequest($_GET);
	$map = new Map("./yeardetail.php");
	$map->setFromRequest($_GET);
	$map->draw();
}
elseif ($view == 'photo')
{
	$sightingQuery = new SightingQuery;
	$sightingQuery->setFromRequest($_GET);
	$sightingQuery->formatPhotos();
}

?>

    </div>
  </body>
</html>
