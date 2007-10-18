<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$sightingInfo = $request->getSightingInfo();

$speciesInfo = getSpeciesInfo($sightingInfo["species_id"]);

$tripInfo = getTripInfo($sightingInfo["trip_id"]);
$tripYear =  substr($tripInfo["date"], 0, 4);

$locationInfo = getLocationInfo($sightingInfo["location_id"]);

$nextPhotoID = performCount(
	"Get Next Photo", 
    "SELECT sightings.id FROM sightings, trip
      WHERE sightings.trip_id=trips.id AND Photo='1' AND CONCAT(trips.Date,sightings.id) > '" . $tripInfo["date"] . $request->getSightingID() . "'
      ORDER BY CONCAT(trips.Date,sightings.id) LIMIT 1");

if ($nextPhotoID != "")
{
	$nextPhotoInfo = performOneRowQuery("Get Next Photo Info", 
       "SELECT sightings.id, date_format(trips.Date, '%b %D, %Y') as niceDate
         FROM sightings, trip WHERE sightings.trip_id=trips.id AND sightings.id='" . $nextPhotoID . "'", false);
	//$nextPhotoLinkText = getThumbForSightingInfo($nextPhotoInfo);
	$nextPhotoLinkText = $nextPhotoInfo["niceDate"];
}
else
{
	$nextPhotoLinkText = "";
}

$prevPhotoID = performCount(
	"Get Previous Photo",
    "SELECT sightings.id FROM sightings, trip
      WHERE sightings.trip_id=trips.id AND Photo='1' AND CONCAT(trips.Date,sightings.id) < '" . $tripInfo["date"] . $request->getSightingID() . "'
      ORDER BY CONCAT(trips.Date,sightings.id) DESC LIMIT 1");

if ($prevPhotoID != "") {
	$prevPhotoInfo = performOneRowQuery("Get Previous Photo Info",
       "SELECT sightings.id, date_format(trips.Date, '%b %D, %Y') as niceDate
         FROM sightings, trip WHERE sightings.trip_id=trips.id AND sightings.id='" . $prevPhotoID . "'", false);
	//$prevPhotoLinkText = getThumbForSightingInfo($prevPhotoInfo);
	$prevPhotoLinkText = $prevPhotoInfo["niceDate"];
}
else
{
	$prevPhotoLinkText = "";
}

htmlHead($speciesInfo["common_name"] . ", " . $tripInfo["niceDate"]);

$request->globalMenu();
?>

<div id="topright-photo">
	<? browseButtons("Photo Detail", "./photodetail.php?sightingid=", $request->getSightingID(),
					 $prevPhotoID, $prevPhotoLinkText, $nextPhotoID, $nextPhotoLinkText); ?>
	  <div class="pagetitle">
          <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["id"] ?>"><?= $speciesInfo["common_name"] ?></a>
<?        editLink("./sightingedit.php?sightingid=" . $request->getSightingID()); ?>
      </div>
      <div class="pagesubtitle">
	     <a href="./tripdetail.php?tripid=<?= $tripInfo["id"] ?>"><?= $tripInfo["niceDate"] ?></a>,
         <a href="./locationdetail.php?locationid=<?= $locationInfo["id"] ?>"><?= $locationInfo["name"] ?>, <?= $locationInfo["state"] ?></a>
      </div>
</div>

<div id="contentright">

<?  if ($sightingInfo["Photo"] == "1")
    {
        $photoFilename = getPhotoFilename($sightingInfo);

	    list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

		<div style="text-align: center">
	      <img width="<?= $width ?>" height="<?= $height ?>" src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>" alt="<?= $speciesInfo['common_name'] ?>">
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

    if (strlen($sightingInfo["notes"]) > 0) { ?>
		<div class="leftcolumn"><p class="report-content"><?= $sightingInfo["notes"] ?></p></a></div>
<?  }

    if (strlen($tripInfo["notes"]) > 0) { ?>
        <div class="heading">
			Trip: <a href="./tripdetail.php?tripid=<?= $tripInfo["id"] ?>"><?= $tripInfo["name"] ?></a>
		</div>
        <div class="leftcolumn"><p class="report-content"><?= $tripInfo["notes"] ?></p></div>
<?  }

    if (strlen($locationInfo["notes"]) > 0) { ?>
	    <div class="heading">
			Location: <a href="./locationdetail.php?locationid=<?= $locationInfo["id"] ?>"><?= $locationInfo["name"] ?></a>
		</div>
        <div class="leftcolumn"><p class="report-content"><?= $locationInfo["notes"] ?></p></div>
<?  }

    if (strlen($speciesInfo["notes"]) > 0) { ?>
	    <div class="heading">
			Species: <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["id"] ?>"><?= $speciesInfo["common_name"] ?></a>
		</div>
	    <div class="leftcolumn"><p class="report-content"><?= $speciesInfo["notes"] ?></p></div>
<?  }

    footer();
 ?>


</div>

<?
htmlFoot();
?>
