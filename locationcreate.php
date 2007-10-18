<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

getEnableEdit() or die("Editing disabled");

// the GET id determines which record to show
$locationid = "";
array_key_exists('locationid', $_GET) && $locationID = $_GET['locationid'];

// the POST id determines which record to update
$postLocationID = "";
array_key_exists('locationid', $_POST) && $postLocationID = $_POST['locationid'];
array_key_exists('locationid', $_POST) && $postLocationInfo = getLocationInfo($_POST['locationid']);

// The SAVE and NEW buttons determine whether to update or createa a new record
$save = "";
array_key_exists('Save', $_POST) && $save = $_POST['Save'];
echo "<!-- " . $save . " -->";
$new = "";
array_key_exists('New', $_POST) && $new = $_POST['New'];

// if NEW, set the POST id to a new unique location id
if ($new == "New") { $postLocationID = 1 + performCount("Find new id", "select max(id) from location"); }

// if we have a POST id and either a new or a save button, then time to update
if ($postLocationID != "") {
	
	$name = postValue("name");
	$reference_url = postValue("reference_url");
	$city = postValue("City");
	$county = postValue("county");
	$state = postValue("state");
	$notes = postValue("notes");
	$latLongSystem = postValue("LatLongSystem");
	$latitude = postValue("Latitude");
	$longitude = postValue("Longitude");
	$photo = postValue("Photo");

	if ($save == "Save/Rename")
	{
	    if ($name != $postLocationInfo['Name'])
		{
		    $alreadyHasNewName = performCount("already named that", "SELECT COUNT(*) FROM location WHERE Name='" . mysql_escape_string($name) . "'");
		    if ($alreadyHasNewName != "0") { die("Location name '" . $name . "' already in use."); }

			performQuery("Update sightings", "UPDATE sightings SET ".
						 "LocationName='" . $name . "' WHERE LocationName='" . mysql_escape_string($postLocationInfo['Name']) . "'");

			echo "location renamed";
		}

		performQuery("Update location", "UPDATE location SET ".
					 "Name='" . $name . "', " .
					 "reference_url='". $reference_url . "', " .
					 "City='" . $city . "', " .
					 "County='" . mysql_escape_string($county) . "', " .
					 "State='". $state . "', " .
					 "Notes='" . $notes . "', " .
					 "LatLongSystem='" . $latLongSystem . "', " .
					 "Latitude='" . $latitude . "', " .
					 "Longitude='" . $longitude . "', " .
					 "Photo='" . $photo . "' where id='" . $postLocationID . "'");

	}
	else if ($new != "")
	{
		performQuery("Create new location", "INSERT INTO location VALUES (" . $postLocationID . ", '" .
					 $name . "', '" .
					 $reference_url . "', '" .
					 $city . "', '" .
					 $county . "', '" .
					 $state . "', '" .
					 $notes . "', '" .
					 $latLongSystem . "', '" .
					 $latitude . "', '" .
					 $longitude . "', '" .
					 $photo . "');");
	}

	$locationID = $postLocationID;
}

$locationInfo = getLocationInfo($locationID);

htmlHead($locationInfo["name"] . ", " .  $locationInfo["state"]);


$request->globalMenu();
?>

<div id="topright-location">
 <? locationBrowseButtons("./locationcreate.php", $locationID, "lists"); ?>
  <a href="./locationdetail.php?locationid=<?= $locationInfo["id"] ?>">
  <div class="pagetitle"><?= $locationInfo["name"] ?></div></a>
</div>

<div id="contentright">

<?
?>

<form method="post" action="./locationcreate.php?locationid=<?= $locationID ?>">

<table class="report-content" width="100%">
  <tr>
	<td class="fieldlabel">Name</td>
	<td><input type="text" name="name" value="<?= $locationInfo['Name'] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">reference_url</td>
	<td><input type="text" name="reference_url" value="<?= $locationInfo['reference_url'] ?>" size="60"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">City</td>
	<td><input type="text" name="City" value="<?= $locationInfo['City'] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">County</td>
	<td><input type="text" name="county" value="<?= $locationInfo['County'] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">State</td>
	<td><input type="text" name="state" value="<?= $locationInfo['State'] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">LatLongSystem</td>
	<td><input type="text" name="LatLongSystem" value="<?= $locationInfo['LatLongSystem'] ?>" size="20"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Latitude</td>
	<td><input type="text" name="Latitude" value="<?= $locationInfo['Latitude'] ?>" size="20"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Longitude</td>
	<td><input type="text" name="Longitude" value="<?= $locationInfo['Longitude'] ?>" size="20"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Photo</td>
	<td><input type="checkbox" name="Photo" value="1" <?php if ($locationInfo["Photo"] == "1") { echo "checked"; } ?> /></td>
  </tr>
  <tr>
	<td class="fieldlabel">Notes</td>
    <td><textarea name="notes" cols="60" rows="20"><?= stripslashes($locationInfo['Notes']) ?></textarea></td>
  </tr>
  <tr>
	<td><input type="hidden" name="locationid" value="<?= $locationID ?>"/></td>
	<td><input type="submit" name="Save" value="Save/Rename"/><input type="submit" name="New" value="New"/></td>
  </tr>
</table>

</form>

<?
footer();
?>

</div>

<?
htmlFoot();
?>
