
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");

//
// *************** modules for the front page
//

function speciesOfTheDay()
{
	// pick a species
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
	  <div class="heading">Species of the Day</div>
        <div class="pagesubtitle"><?= $info["LatinName"] ?></div>
	    <div class="titleblock">
          <span class="subheading"><?= $info["CommonName"] ?></span>

	  <? if (mysql_num_rows($photos) > 0)
	     {
			 $photoInfo = mysql_fetch_array($photos);
			 $photoFilename = getPhotoFilename($photoInfo);
			 list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

			 <img width="300px" src="<?= getPhotoURLForSightingInfo($photoInfo) ?>" alt="bird">

	  <? } ?>

          <? $map->draw(); ?>

	  </div><?
}

function latestTrips()
{
	$numberOfTrips = 8;
	$latestTrips = performQuery("select *, date_format(Date, '%M %e, %Y') AS niceDate from trip order by Date desc LIMIT " . $numberOfTrips);
 ?>
	<div class="heading">Latest Trips</div>

<?  for ($index = 0; $index < $numberOfTrips; $index++)
	{
		$info = mysql_fetch_array($latestTrips);
		$tripSpeciesCount = performCount("SELECT COUNT(DISTINCT(sighting.objectid)) from sighting where sighting.TripDate='" . $info["Date"] . "'"); ?>

		<div class="pagesubtitle"><?= $info["niceDate"] ?></div>

		<div class="titleblock">
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

globalMenu();
?>

    <div class=contentright>

	  <img align="right" src="./images/logo.jpg" width="104" height="105" class="inlinepict"/>

	  <div class="logotype">birdWalker</div>

	  <p>Welcome to <code>birdWalker</code>! This website contains Bill and Mary&#39;s birding field notes, including
	  trip, county, state, and year lists. Our latest trips are listed below, other indices
	  are available from the links on the left.</p>

	  <table>
	    <tr valign="top">
	      <td width="300px">
	        <? speciesOfTheDay(); ?>
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
