
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$sightingInfo = $request->getSightingInfo();

$speciesInfo = performOneRowQuery("SELECT * FROM species WHERE Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
$tripInfo = performOneRowQuery("
    SELECT *, date_format(Date, '%W,  %M %e, %Y') AS niceDate
      FROM trip WHERE Date='" . $sightingInfo["TripDate"] . "'");
$tripYear =  substr($tripInfo["Date"], 0, 4);
$locationInfo = performOneRowQuery("SELECT * FROM location WHERE Name='" . $sightingInfo["LocationName"] . "'");

$nextPhotoID = performCount("
    SELECT objectid FROM sighting
      WHERE Photo='1' AND CONCAT(TripDate,objectid) > '" . $sightingInfo["TripDate"] . $request->getSightingID() . "'
      ORDER BY CONCAT(TripDate,objectid) LIMIT 1");

if ($nextPhotoID != "") {
	$nextPhotoInfo = getSightingInfo($nextPhotoID);
	//$nextPhotoLinkText = getThumbForSightingInfo($nextPhotoInfo);
	$nextPhotoLinkText = $nextPhotoInfo["niceDate"];
}
else
{
	$nextPhotoLinkText = "";
}

$prevPhotoID = performCount("
    SELECT objectid FROM sighting
      WHERE Photo='1' AND CONCAT(TripDate,objectid) < '" . $sightingInfo["TripDate"] . $request->getSightingID() . "'
      ORDER BY CONCAT(TripDate,objectid) DESC LIMIT 1");

if ($prevPhotoID != "") {
	$prevPhotoInfo = getSightingInfo($prevPhotoID);
	//$prevPhotoLinkText = getThumbForSightingInfo($prevPhotoInfo);
	$prevPhotoLinkText = $prevPhotoInfo["niceDate"];
}
else
{
	$prevPhotoLinkText = "";
}

htmlHead($speciesInfo["CommonName"] . ", " . $tripInfo["niceDate"]);

globalMenu();
navTrailPhotos();
?>

<div class="contentright">
	<? browseButtons("Photo Detail", "./photodetail.php?sightingid=", $request->getSightingID(), $prevPhotoID,$prevPhotoLinkText, $nextPhotoID, $nextPhotoLinkText); ?>

  <div class="titleblock">
	  <div class="pagetitle">
          <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["objectid"] ?>"><?= $speciesInfo["CommonName"] ?></a>
<?        editLink("./sightingedit.php?sightingid=" . $request->getSightingID()); ?>
      </div>
      <div class="pagesubtitle">
	     <a href="./tripdetail.php?tripid=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["niceDate"] ?></a>,
         <a href="./locationdetail.php?locationid=<?= $locationInfo["objectid"] ?>"><?= $locationInfo["Name"] ?>, <?= $locationInfo["State"] ?></a>
      </div>
  </div>

<?  if ($sightingInfo["Photo"] == "1")
    {
        $photoFilename = getPhotoFilename($sightingInfo);

	    list($width, $height, $type, $attr) = getimagesize("./images/photo/" . $photoFilename); ?>

	    <img width=<?= $width ?> height=<?= $height ?> src="<?= getPhotoURLForSightingInfo($sightingInfo) ?>" alt="<?= $speciesInfo['CommonName'] ?>">
        <div class=copyright>@<?= $tripYear ?> W. F. Walker</div>
<?  }

    if (strlen($sightingInfo["Notes"]) > 0) { ?>
		<p class=report-content><?= $sightingInfo["Notes"] ?></p>
<?  }

    if (strlen($tripInfo["Notes"]) > 0) { ?>
        <div class=heading>Trip: <a href="./tripdetail.php?tripid=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["Name"] ?></a></div>
        <p class=report-content><?= $tripInfo["Notes"] ?></p>
<?  }

    if (strlen($locationInfo["Notes"]) > 0) { ?>
	    <div class=heading>Location: <a href="./locationdetail.php?locationid=<?= $locationInfo["objectid"] ?>"><?= $locationInfo["Name"] ?></a></div>
        <p class=report-content><?= $locationInfo["Notes"] ?></p>
<?  }

    if (strlen($speciesInfo["Notes"]) > 0) { ?>
	    <div class=heading>Species: <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["objectid"] ?>"><?= $speciesInfo["CommonName"] ?></a></div>
	    <p class=report-content><?= $speciesInfo["Notes"] ?></p>
<?  }

    footer();
 ?>


</div>

<?
htmlFoot();
?>