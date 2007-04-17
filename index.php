<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");

//
// *************** modules for the front page
//

function welcomeMessage()
{
  $localtimearray = localtime(time(), 1);
  $monthNum = $localtimearray["tm_mon"] + 1;
  $dayNum = $localtimearray["tm_mday"];

 ?>
	  <div class="subheading">Welcome</div>

      <div class="summaryblock">
	  <div class="report-content">
	  <img class="inlinepict" src="./images/bill.jpg" align="right"/>
		This website contains the birding field notes of Bill Walker and Mary Wisnewski, including
	  <a href="./tripindex.php">trip</a>,
	  <a href="./countyindex.php">county</a>,
	  <a href="./stateindex.php">state</a>, and
	  <a href="./speciesindex.php?view=speciesbyyear">year</a> lists.
	  You can also see what happened <a href="./onthisdate.php?month=<?= $monthNum ?>&dayofmonth=<?= $dayNum ?>">on this date</a>.
		   Our latest trips are listed to the right, other indices
	  are available from the links on the left.
      </div>
	  </div>
	  <p>&nbsp;</p>
<?
}

function dashboard()
{
    $yearBirds = performCount("year birds", "
      SELECT COUNT(DISTINCT species.objectid)
        FROM species, sighting
        WHERE species.Abbreviation=sighting.SpeciesAbbreviation
        AND Year(sighting.TripDate)='2006'");

    $countyYearBirds = performCount("county year birds", "
      SELECT COUNT(DISTINCT species.objectid)
        FROM species, sighting, location
        WHERE species.Abbreviation=sighting.SpeciesAbbreviation
        AND Year(sighting.TripDate)='2006' AND
        sighting.LocationName=location.Name AND location.County='Santa Clara'");

    $stateYearBirds = performCount("state year birds", "
      SELECT COUNT(DISTINCT species.objectid)
        FROM species, sighting, location
        WHERE species.Abbreviation=sighting.SpeciesAbbreviation
        AND Year(sighting.TripDate)='2006' AND
        sighting.LocationName=location.Name AND location.State='CA'");


?>
	<div class="subheading">Latest Counts</div>
	<div class="summaryblock">
	   <table class="report-content">
	     <tr><td>ABA</td><td>State</td><td>County</td></tr>
	     <tr><td><?= $yearBirds ?></td><td><?= $stateYearBirds ?><td><?= $countyYearBirds ?></td></tr>
	   </table>
	</div>
	  <p>&nbsp;</p>
<?
}

function birdOfTheDay()
{
    $today = performCount("format date", "select date_format(current_date, '%M %D, %Y')");

	// pick a bird
	$speciesRequest = new Request;
	$speciesQuery = new SpeciesQuery($speciesRequest);
	$info = $speciesQuery->getOneRandom();

	// make a small map of sightings for that species
	$mapRequest = new Request;
	$mapRequest->setMapWidth(300);
	$mapRequest->setMapHeight(300);
	$mapRequest->setLatitude(38.25389517844);
	$mapRequest->setLongitude(-98.953048706054);
	$mapRequest->setScale(28);
	$mapRequest->setBackground("relief");
	$mapRequest->setSpeciesID($info["objectid"]);
	$sightingQuery = new SightingQuery($mapRequest);
	$map = new Map("pants", $mapRequest);
	
	$photos = $sightingQuery->performPhotoQuery();

?>

		<div class="subheading">Bird of the Day</div>

		<div class="superheading"><?= $today ?></div>

	    <div class="summaryblock">
		  <div class="report-content">
		    Randomly chosen from among all the species Bill has photographed.
		    The map below the photograph marks locations where we have observed this species.
		  </div>

          <div class="subheading"><a href="./speciesdetail.php?speciesid=<?=$info['objectid']?>"><?= $info["CommonName"] ?></a></div>

	  <? if (mysql_num_rows($photos) > 0)
	     {
			 $photoInfo = mysql_fetch_array($photos);
			 $photoFilename = getPhotoFilename($photoInfo);
			 list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

			 <a href="./speciesdetail.php?speciesid=<?=$info['objectid']?>">
			   <img width="300px" border="0" src="<?= getPhotoURLForSightingInfo($photoInfo) ?>" alt="bird">
			 </a>

	  <? } ?>

		   <div class="subheading"><a href="./speciesdetail.php?speciesid=<?=$info['objectid']?>">Our Sightings</a></div>
          <? $map->draw(); ?>

	  </div>
     <p>&nbsp;</p><?
}

function latestTrips()
{
	$numberOfTrips = 8;
	$latestTrips = performQuery("Get Latest Trips", "SELECT *, " . longNiceDateColumn() . " FROM trip ORDER BY Date DESC LIMIT " . $numberOfTrips);

    TripQuery::formatSummariesForDBQuery($latestTrips);
}

//
// ********************* begin the front page
//

htmlHead("Home");
$request = new Request;
$request->globalMenu();
?>

<?  topRightBanner(); ?>
    <div id="contentright">

	  <table>
	    <tr valign="top">
	      <td class="leftcolumn" width="300px">
	        <? welcomeMessage(); ?>
	        <? birdOfTheDay(); ?>
	      </td>

	      <td class="rightcolumn" width="50%">
	        <? latestTrips(); ?>
          </td>
	    </tr>
	  </table>

<?    footer();
?>
    </div>

<?
htmlFoot();
?>
