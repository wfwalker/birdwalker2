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

// if NEW, set the POST id to a new unique sighting objectid
if ($new == "New") { $postSightingID = 1 + performCount("Find highest sighting ID", "select max(objectid) from sighting"); }

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
					 "UPDATE sighting SET SpeciesAbbreviation='" . $speciesAbbreviation . 
					 "', LocationName='" . $locationName . 
					 "', TripDate='" . $tripDate . 
					 "', Notes='" . $notes . 
					 "', Photo='" . $photo . 
					 "', Exclude='" . $exclude . 
					 "' where objectid='" . $postSightingID . "'");
	} else if ($new != "") {
		performQuery("Insert a new sighting",
					 "insert into sighting values (". $postSightingID .
					 ", '" . $speciesAbbreviation . 
					 "', '" . $locationName . 
					 "', '" . $notes . 
					 "', '" . $exclude . 
					 "', '" . $photo . 
					 "', '" . $tripDate . "');");
	} else if ($delete != "") {
		performQuery("Delete a sighting",
					 "delete from sighting where objectid='" . $postSightingID . "'");
		$postSightingID--;
	}

	$sightingID = $postSightingID;			   
}

$sightingInfo = getSightingInfo($sightingID);

if (performCount("Test for Species Info", "SELECT COUNT(*) from species where Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'")  == 1)
{
	$speciesInfo = performOneRowQuery("Get Species Info", "SELECT * from species where Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
}
else
{
	$speciesInfo = "";
}

$tripInfo = performOneRowQuery("Get Trip Info", "SELECT *, " . shortNiceDateColumn() . " FROM trip WHERE Date='" . $sightingInfo["TripDate"] . "'");
$locationInfo = performOneRowQuery("Get Location Info", "SELECT * from location where Name='" . $sightingInfo["LocationName"] . "'");
$stateInfo = getStateInfoForAbbreviation($locationInfo["State"]);

$locationList = performQuery("Get Location List", "SELECT Name, objectid from location");

if ($speciesInfo == "") {
    htmlHead("Invalid Species Abbreivation, " . $tripInfo["niceDate"]);
} else {
    htmlHead($speciesInfo["CommonName"] . ", " . $tripInfo["niceDate"]);
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
        <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["objectid"] ?>"><?= $speciesInfo["CommonName"] ?></a>
	<? } ?>
  </div>

  <div class="pagesubtitle">
    <a href="./countydetail.php?stateid=<?= $stateInfo["objectid"] ?>&county=<?= $locationInfo["County"] ?>"><?= $locationInfo["County"] ?> County</a>, 
    <a href="./statedetail.php?stateid=<?= $stateInfo["objectid"] ?>"><?= $stateInfo["Name"] ?></a>,
    <a href="./tripdetail.php?tripid=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["niceDate"] ?></a>
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
	<td><input type="text" name="SpeciesAbbreviation" value="<?= $sightingInfo["SpeciesAbbreviation"] ?>" size="6"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Location</td>
	<td>
	  <select name="LocationName">
<?php
   while($info = mysql_fetch_array($locationList))
   {
	   if ($info["Name"] == $sightingInfo["LocationName"]) { echo "<option selected>"; } else { echo "<option>"; }
	   echo $info["Name"] . "</option>\n";
   }
?>
	  </select>
	</td>
  </tr>
  <tr>
	<td class="fieldlabel">Notes</td>
	<td><textarea name="Notes" cols="60" rows="20"><?= $sightingInfo["Notes"] ?></textarea></td>
  </tr>
  <tr>
	<td class="fieldlabel">Exclude</td>
	<td><input type="checkbox" name="Exclude" value="1" <?php if ($sightingInfo["Exclude"] == "1") { echo "checked"; } ?> /></td>
  </tr>
  <tr>
	<td class="fieldlabel">Photo</td>
	<td><input type="checkbox" name="Photo" value="1" <?php if ($sightingInfo["Photo"] == "1") { echo "checked"; } ?> /></td>
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
