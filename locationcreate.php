
<?

require_once("./birdwalker.php");

getEnableEdit() or die("Editing disabled");

// the GET id determines which record to show
$locationID = $_GET['locationid'];

// the POST id determines which record to update
$postLocationID = $_POST['locationid'];

// The SAVE and NEW buttons determine whether to update or createa a new record
$save = $_POST['Save'];
$new = $_POST['New'];

// if NEW, set the POST id to a new unique location objectid
if ($new == "New") { $postLocationID = 1 + performCount("select max(objectid) from location"); }

// if we have a POST id and either a new or a save button, then time to update
if ($postLocationID != "") {
	
	$name = $_POST['Name'];
	$referenceURL = $_POST['ReferenceURL'];
	$city = $_POST['City'];
	$county = $_POST['County'];
	$state = $_POST['State'];
	$notes = $_POST['Notes'];
	$latLongSystem = $_POST['LatLongSystem'];
	$latitude = $_POST['Latitude'];
	$longitude = $_POST['Longitude'];

	if ($save == "Save")
	{
		performQuery("UPDATE location SET ".
					 "Name='" . $name . "', " .
					 "ReferenceURL='". $referenceURL . "', " .
					 "City='" . $city . "', " .
					 "County='" . $county . "', " .
					 "State='". $state . "', " .
					 "Notes='" . $notes . "', " .
					 "LatLongSystem='" . $latLongSystem . "', " .
					 "Latitude='" . $latitude . "', " .
					 "Longitude='" . $longitude . "' where objectid='" . $postLocationID . "'");
	} else if ($new != "") {
		performQuery("INSERT INTO location VALUES (" . $postLocationID . ", '" .
					 $name . "', '" .
					 $referenceURL . "', '" .
					 $city . "', '" .
					 $county . "', '" .
					 $state . "', '" .
					 $notes . "', '" .
					 $latLongSystem . "', '" .
					 $latitude . "', '" .
					 $longitude . "');");
	}

	$locationID = $postLocationID;
}

$locationInfo = getLocationInfo($locationID);

htmlHead($locationInfo["Name"] . ", " .  $locationInfo["State"]);

globalMenu();
locationBrowseButtons("./locationcreate.php", $locationID, $view);
navTrailLocationDetail($locationInfo, "list");
?>

<div class="contentright">
<div class="titleblock">
  <a href="./locationdetail.php?locationid=<?= $locationInfo["objectid"] ?>">
    <div class=pagetitle><?= $locationInfo["Name"] ?></div>
  <div class=pagesubtitle><?= $locationInfo["niceDate"] ?></div>
</a>
</div>

<?
?>

<form method="post" action="./locationcreate.php?id=<?= $locationID ?>">

<table class=report-content width=100%>
  <tr>
	<td class=fieldlabel>Name</td>
	<td><input type="text" name="Name" value="<?= $locationInfo['Name'] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>ReferenceURL</td>
	<td><input type="text" name="ReferenceURL" value="<?= $locationInfo['ReferenceURL'] ?>" size=60/></td>
  </tr>
  <tr>
	<td class=fieldlabel>City</td>
	<td><input type="text" name="City" value="<?= $locationInfo['City'] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>County</td>
	<td><input type="text" name="County" value="<?= $locationInfo['County'] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>State</td>
	<td><input type="text" name="State" value="<?= $locationInfo['State'] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>LatLongSystem</td>
	<td><input type="text" name="LatLongSystem" value="<?= $locationInfo['LatLongSystem'] ?>" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Latitude</td>
	<td><input type="text" name="Latitude" value="<?= $locationInfo['Latitude'] ?>" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Longitude</td>
	<td><input type="text" name="Longitude" value="<?= $locationInfo['Longitude'] ?>" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Notes</td>
	<td><textarea name="Notes" cols=60 rows=20><?= $locationInfo['Notes'] ?></textarea></td>
  </tr>
  <tr>
	<td><input type="hidden" name="locationid" value="<?= $locationID ?>"/></td>
	<td><input type="submit" name="Save" value="Save"/><input type="submit" name="New" value="New"/></td>
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
