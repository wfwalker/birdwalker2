
<?php

require_once("./birdwalker.php");

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
if ($new == "New") { $postSightingID = 1 + performCount("select max(objectid) from sighting"); }

// if we have a POST id and either a new or a save button, then time to update
if ($postSightingID != "") {
	$speciesAbbreviation = postValue('SpeciesAbbreviation');
	$locationName = postValue('LocationName');
	$tripDate = postValue('TripDate');
	$notes = postValue('Notes');
	$exclude = postValue('Exclude');
	$photo = postValue('Photo');

	if ($save != "") {
		performQuery("update sighting set SpeciesAbbreviation='" . $speciesAbbreviation . 
					 "', LocationName='" . $locationName . 
					 "', TripDate='" . $tripDate . 
					 "', Notes='" . $notes . 
					 "', Photo='" . $photo . 
					 "', Exclude='" . $exclude . 
					 "' where objectid='" . $postSightingID . "'");
	} else if ($new != "") {
		performQuery("insert into sighting values (". $postSightingID .
					 ", '" . $speciesAbbreviation . 
					 "', '" . $locationName . 
					 "', '" . $notes . 
					 "', '" . $exclude . 
					 "', '" . $photo . 
					 "', '" . $tripDate . "');");
	} else if ($delete != "") {
		performQuery("delete from sighting where objectid='" . $postSightingID . "'");
		$postSightingID--;
	}

	$sightingID = $postSightingID;			   
}

$sightingInfo = getSightingInfo($sightingID);
$speciesInfo = performOneRowQuery("select * from species where Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
$tripInfo = performOneRowQuery("select *, date_format(Date, '%W,  %M %e, %Y') as niceDate from trip where Date='" . $sightingInfo["TripDate"] . "'");
$locationInfo = performOneRowQuery("select * from location where Name='" . $sightingInfo["LocationName"] . "'");
$stateInfo = getStateInfoForAbbreviation($locationInfo["State"]);

$locationList = performQuery("select Name, objectid from location");

htmlHead($speciesInfo["CommonName"] . ", " . $tripInfo["niceDate"]);

globalMenu();
navTrail();
?>

<div class="contentright">

<div class=pagesubtitle>

	<? browseButtons("Sighting Detail", "./sightingedit.php?sightingid=", $sightingID,
					 $sightingID - 1, $sightingID - 1, $sightingID + 1, $sightingID + 1); ?>
</div>
<div class="titleblock">
  <div class=pagetitle>
    <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["objectid"] ?>"><?= $speciesInfo["CommonName"] ?></a>
  </div>
  <div class=metadata>
    <a href="./countydetail.php?stateid=<?= $stateInfo["objectid"] ?>&county=<?= $locationInfo["County"] ?>"><?= $locationInfo["County"] ?> County</a>, 
    <a href="./statedetail.php?stateid=<?= $stateInfo["objectid"] ?>"><?= $stateInfo["Name"] ?></a><br/>
    <a href="./tripdetail.php?tripid=<?= $tripInfo["objectid"] ?>"><?= $tripInfo["niceDate"] ?></a><br/>

  </div>
</div>

<form method="post" action="./sightingedit.php?sightingid=<?= $sightingID ?>">

<table class=report-content width=100%>
  <tr>
	<td></td>
	<td><input type="submit" name="New" value="New"/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Abbreviation</td>
	<td><input type="text" name="SpeciesAbbreviation" value="<?= $sightingInfo["SpeciesAbbreviation"] ?>" size=6/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Location</td>
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
	<td class=fieldlabel>Notes</td>
	<td><textarea name="Notes" cols=60 rows=20><?= $sightingInfo["Notes"] ?></textarea></td>
  </tr>
  <tr>
	<td class=fieldlabel>Exclude</td>
	<td><input type="checkbox" name="Exclude" value="1" <?php if ($sightingInfo["Exclude"] == "1") { echo "checked"; } ?> /></td>
  </tr>
  <tr>
	<td class=fieldlabel>Photo</td>
	<td><input type="checkbox" name="Photo" value="1" <?php if ($sightingInfo["Photo"] == "1") { echo "checked"; } ?> /></td>
  </tr>
  <tr>
	<td class=fieldlabel>TripDate</td>
	<td><input type="text" name="TripDate" value="<?= $sightingInfo["TripDate"] ?>" size=20/></td>
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
