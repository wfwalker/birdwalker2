
<?php

require_once("./birdwalker.php");

$sightingID = $_GET['id'];

$sightingInfo = getSightingInfo($sightingID);
$speciesInfo = performOneRowQuery("SELECT * FROM species WHERE Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
$tripInfo = performOneRowQuery("
    SELECT *, date_format(Date, '%W,  %M %e, %Y') AS niceDate
      FROM trip WHERE Date='" . $sightingInfo["TripDate"] . "'");
$tripYear =  substr($tripInfo["Date"], 0, 4);
$locationInfo = performOneRowQuery("SELECT * FROM location WHERE Name='" . $sightingInfo["LocationName"] . "'");

$firstPhotoID = performCount("
    SELECT objectid FROM sighting WHERE Photo='1' ORDER BY CONCAT(TripDate,objectid) LIMIT 1");
$lastPhotoID = performCount("
    SELECT objectid FROM sighting WHERE Photo='1' ORDER BY CONCAT(TripDate, objectid) DESC LIMIT 1");

$nextPhotoID = performCount("
    SELECT objectid FROM sighting
      WHERE Photo='1' AND CONCAT(TripDate,objectid) > '" . $sightingInfo["TripDate"] . $sightingID . "'
      ORDER BY CONCAT(TripDate,objectid) LIMIT 1");
$prevPhotoID = performCount("
    SELECT objectid FROM sighting
      WHERE Photo='1' AND CONCAT(TripDate,objectid) < '" . $sightingInfo["TripDate"] . $sightingID . "'
      ORDER BY CONCAT(TripDate,objectid) DESC LIMIT 1");

if ($nextPhotoID == "") { $nextPhotoID = $sightingID; }
if ($prevPhotoID == "") { $prevPhotoID = $sightingID; }

?>

<html>

  <? htmlHead($speciesInfo["CommonName"] . ", " . $tripInfo["niceDate"]); ?>

  <body>

<?php
globalMenu();
browseButtons("./photodetail.php?id=", $sightingID, $firstPhotoID, $prevPhotoID, $nextPhotoID, $lastPhotoID);
navTrailPhotos();
?>

<div class="contentright">
  <div class=report-content>
    <a href="./tripdetail.php?tripid=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["niceDate"] ?>
  </div>

<div class="titleblock">
	  <div class=pagetitle>
          <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["objectid"] ?>"><?= $speciesInfo["CommonName"] ?></a>
<?        editLink("./sightingedit.php?id=" . $sightingID); ?>
      </div>
      <div class=metadata>
          <a href="./locationdetail.php?id=<?= $locationInfo["objectid"] ?>"><?= $locationInfo["Name"] ?>, <?= $locationInfo["State"] ?></a> 
      </div>
</div>

<?  if ($sightingInfo["Photo"] == "1")
    {
        $photoFilename = getPhotoFilename($sightingInfo);

	    list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

	    <img width=<?= $width ?> height=<?= $height ?> src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>">
        <div class=copyright>@<?= $tripYear ?> W. F. Walker</div>
<?  }

    if (strlen($sightingInfo["Notes"]) > 0) { ?>
		<p class=sighting-notes><?= $sightingInfo["Notes"] ?></p>
<?  }

    if (strlen($tripInfo["Notes"]) > 0) { ?>
        <div class=heading>Trip: <?= $tripInfo["Name"] ?></div>
        <p class=sighting-notes><?= $tripInfo["Notes"] ?></p>
<?  }

    if (strlen($locationInfo["Notes"]) > 0) { ?>
	    <div class=heading>Location: <?= $locationInfo["Name"] ?></div>
        <p class=sighting-notes><?= $locationInfo["Notes"] ?></p>
<?  }

    if (strlen($speciesInfo["Notes"]) > 0) { ?>
	    <div class=heading>Species: <?= $speciesInfo["CommonName"] ?></div>
	    <p class=sighting-notes><?= $speciesInfo["Notes"] ?></p>
<?  } ?>

</div>
</body>
</html>
