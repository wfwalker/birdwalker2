<?php

require_once("./birdwalker.php");
require_once("./request.php");

getEnableEdit() or die("Editing disabled");

$request = new Request;

// the GET id determines which record to show
$sightingID = getValue("sightingid");

$sightingInfo = getSightingInfo($sightingID);
$speciesInfo = getSpeciesInfo($sightingInfo["species_id"]);
$tripInfo = getTripInfo($sightingInfo["trip_id"]);
$tripYear =  substr($tripInfo["date"], 0, 4);
$locationInfo = getLocationInfo($sightingInfo["location_id"]);

htmlHead($speciesInfo["common_name"] . ", " . $tripInfo["niceDate"]);

$request->globalMenu();
?>

<div id="topright-trip">
<? browseButtons("Sighting Detail", "./sightingdetail.php?sightingid=", $sightingID, $sightingID - 1, $sightingID - 1, $sightingID + 1, $sightingID + 1); ?>
  <div class="pagetitle"> <?= $speciesInfo["common_name"] ?></div>

  <div class="pagesubtitle"><?= $locationInfo["county"] ?> County, <?= getStateNameForAbbreviation($locationInfo["state"]) ?></div>
</div>


<div id="contentright">

<table class="report-content" width="100%">
  <tr><td class="heading" colspan="2">Sighting</td></tr>
  <tr><td class="fieldlabel">Species.Abbreviation</td><td><?= $speciesInfo["abbreviation"] ?></td></tr>
  <tr><td class="fieldlabel">Location.Name</td><td><?= $locationInfo["name"] ?></td></tr>
  <tr><td class="fieldlabel">Notes</td><td><?= $sightingInfo["notes"] ?></td></tr>
  <tr><td class="fieldlabel">Exclude</td><td><?= $sightingInfo["Exclude"] ?></td></tr>
  <tr><td class="fieldlabel">Photo</td><td><?= $sightingInfo["Photo"] ?></td></tr>
  <tr><td class="fieldlabel">Trip.Date</td><td><?= $tripInfo["date"] ?></td></tr>

  <tr><td class="heading" colspan="2">Species</td></tr>
  <tr><td class="fieldlabel">Abbreviation</td><td><?= $speciesInfo["abbreviation"] ?></td></tr>
  <tr><td class="fieldlabel">latin_name</td><td><?= $speciesInfo["latin_name"] ?></td></tr>
  <tr><td class="fieldlabel">common_name</td><td><?= $speciesInfo["common_name"] ?></td></tr>
  <tr><td class="fieldlabel">Notes</td><td><?= $speciesInfo["notes"] ?></td></tr>
  <tr><td class="fieldlabel">reference_url</td><td><?= $speciesInfo["reference_url"] ?></td></tr>

  <tr><td class="heading" colspan="2">Trip</td></tr>
  <tr><td class="fieldlabel">Name</td><td><?= $tripInfo["name"] ?></td></tr>
  <tr><td class="fieldlabel">Leader</td><td><?= $tripInfo["Leader"] ?></td></tr>
  <tr><td class="fieldlabel">reference_url</td><td><?= $tripInfo["reference_url"] ?></td></tr>
  <tr><td class="fieldlabel">Name</td><td><?= $tripInfo["name"] ?></td></tr>
  <tr><td class="fieldlabel">Notes</td><td><?= $tripInfo["notes"] ?></td></tr>
  <tr><td class="fieldlabel">Date</td><td><?= $tripInfo["date"] ?></td></tr>

  <tr><td class="heading" colspan="2">Location</td></tr>
  <tr><td class="fieldlabel">Name</td><td><?= $locationInfo["name"] ?></td></tr>
  <tr><td class="fieldlabel">Reference URL</td><td><?= $locationInfo["reference_url"] ?></td></tr>
  <tr><td class="fieldlabel">City</td><td><?= $locationInfo["City"] ?></td></tr>
  <tr><td class="fieldlabel">County</td><td><?= $locationInfo["county"] ?></td></tr>
  <tr><td class="fieldlabel">State</td><td><?= $locationInfo["state"] ?></td></tr>
  <tr><td class="fieldlabel">Notes</td><td><?= $locationInfo["notes"] ?></td></tr>
  <tr><td class="fieldlabel">LatLongSystem</td><td><?= $locationInfo["LatLongSystem"] ?></td></tr>
  <tr><td class="fieldlabel">Latitude</td><td><?= $locationInfo["Latitude"] ?></td></tr>
  <tr><td class="fieldlabel">Longitude</td><td><?= $locationInfo["Longitude"] ?></td></tr>
</table>

<?
footer();
?>

</div>

<?
htmlFoot();
?>
