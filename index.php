
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");

$numberOfTrips = 10;
$latestTrips = performQuery("select *, date_format(Date, '%M %e, %Y') AS niceDate from trip order by Date desc LIMIT " . $numberOfTrips);
$randomPhotoSightings = performQuery("SELECT *, " . dailyRandomSeedColumn() . " FROM sighting WHERE Photo='1' ORDER BY shuffle LIMIT 5");

htmlHead("Home");

globalMenu();
?>

    <div class=contentright>

	  <p>Welcome to <code>birdWalker</code>! This website contains Bill and Mary&#39;s birding field notes, including
	  trip, county, state, and year lists. Our latest trips are listed below, other indices
	  are available from the links on the left.</p>

	  <table>
	  <tr valign="top">
	  <td width="50%">
	  <div class="heading">Species of the Day</div>
<?
	  $request = new Request;
      $request->setMapWidth(300);
      $request->setMapHeight(300);
      $request->setLatitude(38.25389517844);
      $request->setLongitude(-98.953048706054);
      $request->setScale(28);
      $request->setBackground("relief");
	  $speciesQuery = new SpeciesQuery($request);
      $info = $speciesQuery->getOneRandom();
      $request->setSpeciesID($info["objectid"]);
	  $sightingQuery = new SightingQuery($request);
      $map = new Map("pants", $request);

	  $photos = $sightingQuery->performPhotoQuery();
?>
      <div class="pagesubtitle"><?= $info["LatinName"] ?></div>
	  <div class="titleblock">
        <span class="subheading"><?= $info["CommonName"] ?></span>

	  <? if (mysql_num_rows($photos) > 0) { $photoInfo = mysql_fetch_array($photos);
	    $photoFilename = getPhotoFilename($photoInfo);
		list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>
		<img width="300px" src="<?= getPhotoURLForSightingInfo($photoInfo) ?>" alt="bird">
	  <? } ?>

	  <? $map->draw(); ?>

	  </div>
	  </td>
	  <td width="50%">
	  <div class="heading">Latest Trips</div>

<?    for ($index = 0; $index < $numberOfTrips; $index++)
	  {
		  $info = mysql_fetch_array($latestTrips);
          $tripSpeciesCount = performCount("SELECT COUNT(DISTINCT(sighting.objectid)) from sighting where sighting.TripDate='" . $info["Date"] . "'"); ?>

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

<?	  } ?>

      </td>
	  </tr>
	  </table>

<?    footer();
?>
    </div>

<?
htmlFoot();
?>
