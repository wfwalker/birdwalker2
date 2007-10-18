<?php

require_once("./birdwalker.php");
require_once("./request.php");

getEnableEdit() or die("Editing disabled");

// the GET id determines which record to show
$sightingID = getValue("sightingid");

// the POST id determines which record to update
$postSightingID = postValue("sightingid");

// The SAVE and NEW buttons determine whether to update or createa a new record
$save = postValue("Save");
$new = postValue("New");
$delete = postValue("Delete");

// if NEW, set the POST id to a new unique sighting id
if ($new == "New") { $postSightingID = 1 + performCount("Find highest sighting ID", "select max(id) from sighting"); }

// if we have a POST id and either a new or a save button, then time to update
if ($postSightingID != "") {
	$speciesAbbreviation = postValue('SpeciesAbbreviation');
	$locationName = postValue('LocationName');
	$tripDate = postValue('TripDate');
	$notes = postValue('Notes');
	$exclude = postValue('Exclude');
	$photo = postValue('Photo');

	if ($save != "") {
		performQuery("Update the sighting", 
					 "UPDATE sightings SET SpeciesAbbreviation='" . mysql_escape_string($speciesAbbreviation) . 
					 "', LocationName='" . $locationName . 
					 "', TripDate='" . mysql_escape_string($tripDate) . 
					 "', Notes='" . mysql_escape_string($notes) . 
					 "', Photo='" . mysql_escape_string($photo) . 
					 "', Exclude='" . $exclude . 
					 "' where id='" . $postSightingID . "'");
	} else if ($new != "") {
		performQuery("Insert a new sighting",
					 "insert into sighting values (". $postSightingID .
					 ", '" . mysql_escape_string($speciesAbbreviation) . 
					 "', '" . mysql_escape_string($locationName) . 
					 "', '" . mysql_escape_string($notes) . 
					 "', '" . mysql_escape_string($exclude) . 
					 "', '" . $photo . 
					 "', '" . $tripDate . "');");
	} else if ($delete != "") {
		performQuery("Delete a sighting",
					 "delete from sighting where id='" . $postSightingID . "'");
		$postSightingID--;
	}

	$sightingID = $postSightingID;			   
}

$sightingInfo = getSightingInfo($sightingID);

if (performCount("Test for Species Info", "SELECT COUNT(*) from species where id='" . $sightingInfo["species_id"] . "'")  == 1)
{
	$speciesInfo = getSpeciesInfo($sightingInfo["species_id"]);
}
else
{
	$speciesInfo = "";
}

$tripInfo = getTripInfo($sightingInfo["trip_id"]);
$locationInfo = getLocationInfo($sightingInfo["location_id"]);
$stateInfo = getStateInfoForAbbreviation($locationInfo["state"]);

$locationList = performQuery("Get Location List", "SELECT name, id from locations ORDER BY name");

if ($speciesInfo == "") {
    htmlHead("Invalid Species Abbreivation, " . $tripInfo["niceDate"]);
} else {
    htmlHead($speciesInfo["common_name"] . ", " . $tripInfo["niceDate"]);
}

$request = new Request;

$request->globalMenu();
?>

<div id="topright-trip">
	<? browseButtons("Sighting Detail", "./sightingedit.php?sightingid=", $sightingID,
					 $sightingID - 1, $sightingID - 1, $sightingID + 1, $sightingID + 1); ?>

  <div class="pagetitle">
	<? if ($speciesInfo == "") { ?>
	    Invalid Species Abbreviation
    <? } else { ?>
        <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["id"] ?>"><?= $speciesInfo["common_name"] ?></a>
	<? } ?>
  </div>

  <div class="pagesubtitle">
    <a href="./countydetail.php?stateid=<?= $stateInfo["id"] ?>&county=<?= $locationInfo["county"] ?>"><?= $locationInfo["county"] ?> County</a>, 
    <a href="./statedetail.php?stateid=<?= $stateInfo["id"] ?>"><?= $stateInfo["name"] ?></a>,
    <a href="./tripdetail.php?tripid=<?= $tripInfo["id"] ?>"><?= $tripInfo["niceDate"] ?></a>
  </div>
</div>

<div id="contentright">

<form method="post" action="./sightingedit.php?sightingid=<?= $sightingID ?>">

<table class="report-content" width="100%">
  <tr>
	<td></td>
	<td><input type="submit" name="New" value="New"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Abbreviation</td>
	<td><input type="text" name="SpeciesAbbreviation" value="<?= $speciesInfo["abbreviation"] ?>" size="6"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Location</td>
	<td>
	  <select name="LocationName">
<?php
   while($info = mysql_fetch_array($locationList))
   {
	   if ($info["id"] == $sightingInfo["location_id"]) { echo "<option selected>"; } else { echo "<option>"; }
	   echo $info["name"] . "</option>\n";
   }
?>
	  </select>
	</td>
  </tr>
  <tr>
	<td class="fieldlabel">Notes</td>
    <td><textarea name="notes" cols="60" rows="20"><?= stripslashes($sightingInfo["notes"]) ?></textarea></td>
  </tr>
  <tr>
	<td class="fieldlabel">Exclude</td>
	<td><input type="checkbox" name="Exclude" value="1" <?php if ($sightingInfo["exclude"] == "1") { echo "checked"; } ?> /></td>
  </tr>
  <tr>
	<td class="fieldlabel">Photo</td>
	<td><input type="checkbox" name="photo" value="1" <?php if ($sightingInfo["photo"] == "1") { echo "checked"; } ?> /></td>
  </tr>
  <tr>
	<td class="fieldlabel">TripDate</td>
	<td><input type="text" name="TripDate" value="<?= $sightingInfo["TripDate"] ?>" size="20"/></td>
  </tr>
  <tr>
	<td><input type="hidden" name="sightingid" value="<?= $sightingID ?>"/></td>
	<td><input type="submit" name="Save" value="Save"/></td>
  </tr>
</table>

<p><input type="submit" name="Delete" value="Delete"/></p>

</form>

<?
footer();
?>

</div>

<?
htmlFoot();
?>
