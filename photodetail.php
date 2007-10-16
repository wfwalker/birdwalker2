<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$sightingInfo = $request->getSightingInfo();

$speciesInfo = getSpeciesInfo($sightingInfo["species_id"]);

$tripInfo = getTripInfo($sightingInfo["trip_id"]);
$tripYear =  substr($tripInfo["Date"], 0, 4);

$locationInfo = getLocationInfo($sightingInfo["location_id"]);

$nextPhotoID = performCount(
	"Get Next Photo", 
    "SELECT sighting.id FROM sighting, trip
      WHERE sighting.trip_id=trip.id AND Photo='1' AND CONCAT(trip.Date,sighting.id) > '" . $tripInfo["Date"] . $request->getSightingID() . "'
      ORDER BY CONCAT(trip.Date,sighting.id) LIMIT 1");

if ($nextPhotoID != "")
{
	$nextPhotoInfo = performOneRowQuery("Get Next Photo Info", 
       "SELECT sighting.id, date_format(trip.Date, '%b %D, %Y') as niceDate
         FROM sighting, trip WHERE sighting.trip_id=trip.id AND sighting.id='" . $nextPhotoID . "'", false);
	//$nextPhotoLinkText = getThumbForSightingInfo($nextPhotoInfo);
	$nextPhotoLinkText = $nextPhotoInfo["niceDate"];
}
else
{
	$nextPhotoLinkText = "";
}

$prevPhotoID = performCount(
	"Get Previous Photo",
    "SELECT sighting.id FROM sighting, trip
      WHERE sighting.trip_id=trip.id AND Photo='1' AND CONCAT(trip.Date,sighting.id) < '" . $tripInfo["Date"] . $request->getSightingID() . "'
      ORDER BY CONCAT(trip.Date,sighting.id) DESC LIMIT 1");

if ($prevPhotoID != "") {
	$prevPhotoInfo = performOneRowQuery("Get Previous Photo Info",
       "SELECT sighting.id, date_format(trip.Date, '%b %D, %Y') as niceDate
         FROM sighting, trip WHERE sighting.trip_id=trip.id AND sighting.id='" . $prevPhotoID . "'", false);
	//$prevPhotoLinkText = getThumbForSightingInfo($prevPhotoInfo);
	$prevPhotoLinkText = $prevPhotoInfo["niceDate"];
}
else
{
	$prevPhotoLinkText = "";
}

htmlHead($speciesInfo["CommonName"] . ", " . $tripInfo["niceDate"]);

$request->globalMenu();
?>

<div id="topright-photo">
	<? browseButtons("Photo Detail", "./photodetail.php?sightingid=", $request->getSightingID(),
					 $prevPhotoID, $prevPhotoLinkText, $nextPhotoID, $nextPhotoLinkText); ?>
	  <div class="pagetitle">
          <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["id"] ?>"><?= $speciesInfo["CommonName"] ?></a>
<?        editLink("./sightingedit.php?sightingid=" . $request->getSightingID()); ?>
      </div>
      <div class="pagesubtitle">
	     <a href="./tripdetail.php?tripid=<?= $tripInfo["id"] ?>"><?= $tripInfo["niceDate"] ?></a>,
         <a href="./locationdetail.php?locationid=<?= $locationInfo["id"] ?>"><?= $locationInfo["Name"] ?>, <?= $locationInfo["State"] ?></a>
      </div>
</div>

<div id="contentright">

<?  if ($sightingInfo["Photo"] == "1")
    {
        $photoFilename = getPhotoFilename($sightingInfo);

	    list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

		<div style="text-align: center">
	      <img width="<?= $width ?>" height="<?= $height ?>" src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>" alt="<?= $speciesInfo['CommonName'] ?>">
          <div class="copyright">&copy; <?= $tripYear ?> W. F. Walker</div>
		</div>
<?
		$exif = exif_read_data("./images/photo/" . $photoFilename, "IFD0");
		if ($exif != "")
		{ ?>
		    <div class="heading">Details</div>
			<div class="leftcolumn">
			  <p class="report=content"><?= $exif["Model"] . ", ISO " . $exif["ISOSpeedRatings"] . ", " . $exif["ExposureTime"] . "\", " . $exif["FocalLength"] . "mm, " . $exif["COMPUTED"]["ApertureFNumber"] ?><p>
			</div>
<?      }
    }

    if (strlen($sightingInfo["Notes"]) > 0) { ?>
		<div class="leftcolumn"><p class="report-content"><?= $sightingInfo["Notes"] ?></p></a></div>
<?  }

    if (strlen($tripInfo["Notes"]) > 0) { ?>
        <div class="heading">
			Trip: <a href="./tripdetail.php?tripid=<?= $tripInfo["id"] ?>"><?= $tripInfo["Name"] ?></a>
		</div>
        <div class="leftcolumn"><p class="report-content"><?= $tripInfo["Notes"] ?></p></div>
<?  }

    if (strlen($locationInfo["Notes"]) > 0) { ?>
	    <div class="heading">
			Location: <a href="./locationdetail.php?locationid=<?= $locationInfo["id"] ?>"><?= $locationInfo["Name"] ?></a>
		</div>
        <div class="leftcolumn"><p class="report-content"><?= $locationInfo["Notes"] ?></p></div>
<?  }

    if (strlen($speciesInfo["Notes"]) > 0) { ?>
	    <div class="heading">
			Species: <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["id"] ?>"><?= $speciesInfo["CommonName"] ?></a>
		</div>
	    <div class="leftcolumn"><p class="report-content"><?= $speciesInfo["Notes"] ?></p></div>
<?  }

    footer();
 ?>


</div>

<?
htmlFoot();
?>
