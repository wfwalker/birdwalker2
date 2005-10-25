
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");

//
// *************** modules for the front page
//

function birdOfTheDay()
{
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
	  <div class="heading">Bird of the Day</div>

        <div class="superheading"><?= $info["LatinName"] ?></div>
	    <div class="summaryblock">
          <span class="heading"><a href="./speciesdetail.php?speciesid=<?=$info['objectid']?>"><?= $info["CommonName"] ?></a></div>

	  <? if (mysql_num_rows($photos) > 0)
	     {
			 $photoInfo = mysql_fetch_array($photos);
			 $photoFilename = getPhotoFilename($photoInfo);
			 list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

			 <a href="./speciesdetail.php?speciesid=<?=$info['objectid']?>"><img width="300px" border="0" src="<?= getPhotoURLForSightingInfo($photoInfo) ?>" alt="bird"></a>

	  <? } ?>

          <? $map->draw(); ?>

	  </div><?
}

function latestTrips()
{
	$numberOfTrips = 8;
	$latestTrips = performQuery("SELECT *, " . niceDateColumn() . " FROM trip ORDER BY Date DESC LIMIT " . $numberOfTrips);
 ?>
	<div class="heading">Latest Trips</div>

<?  for ($index = 0; $index < $numberOfTrips; $index++)
	{
		$info = mysql_fetch_array($latestTrips);
		$tripSpeciesCount = performCount("SELECT COUNT(DISTINCT(sighting.objectid)) from sighting where sighting.TripDate='" . $info["Date"] . "'"); ?>

		<div class="superheading"><?= $info["niceDate"] ?></div>

		<div class="summaryblock">
		    <span class="heading">
		        <a href="./tripdetail.php?tripid=<?=$info["objectid"]?>">
<?                 rightThumbnail("SELECT * FROM sighting WHERE Photo='1' AND TripDate='" . $info["Date"] . "' LIMIT 1", false); ?>
                   <?= $info["Name"] ?>
                </a>
            </span>
            <div class="subheading"><?= $tripSpeciesCount ?> species</div>
        </div>

        <div class=report-content><?= $info["Notes"] ?><br clear="all"/></div>
		<p>&nbsp;</p>

<?  }
}

//
// ********************* begin the front page
//

htmlHead("Home");
$request = new Request;
$request->globalMenu();
?>

    <div class="topright">
	  <div class="logotype"><img src="./images/logotype.gif" width="389" height="61" alt="birdWalker"/></div>
	</div>

    <div class="contentright">


	  <p>Welcome to <code>birdWalker</code>! This website contains Bill Walker and Mary Wisnewski&#39;s birding field notes, including
	  <a href="./tripindex.php">trip</a>,
	  <a href="./countyindex.php">county</a>,
	  <a href="./stateindex.php">state</a>, and
	  <a href="./speciesindex.php?view=speciesbyyear">year</a> lists.
	  Our latest trips are listed below, other indices
	  are available from the links on the left.</p>

	  <table>
	    <tr valign="top">
	      <td width="300px">
	        <? birdOfTheDay(); ?>
	      </td>

	      <td width="15px"></td>

	      <td width="50%">
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
