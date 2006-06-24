
<?php

require_once("./birdwalker.php");
require_once("./request.php");

getEnableEdit() or die("Editing disabled");

$save = ""; array_key_exists("Save", $_POST) && $save = $_POST['Save'];
$abbreviations = ""; array_key_exists("Abbreviations", $_POST) && $abbreviations = $_POST['Abbreviations'];
$notes = ""; array_key_exists("Notes", $_POST) && $notes = $_POST['Notes'];
$locationName = ""; array_key_exists("LocationName", $_POST) && $locationName = $_POST['LocationName'];
$leader = ""; array_key_exists("Leader", $_POST) && $leader = $_POST['Leader'];
$tripDate = ""; array_key_exists("TripDate", $_POST) && $tripDate = $_POST['TripDate'];
$tripName = ""; array_key_exists("TripName", $_POST) && $tripName = $_POST['TripName'];

$locationList = performQuery("Get All Locations", "SELECT Name, objectid FROM location ORDER BY Name");
$dateArray = getdate();
$dateString = $dateArray["year"] . "-" . $dateArray["mon"] . "-" .  $dateArray["mday"];
$sightingID = performCount("Get Highest Sighting ID", "SELECT MAX(objectid) from sighting;");
$tripID = performCount("Get Highest Trip ID", "SELECT MAX(objectid) from trip;");

htmlHead("Create a trip");

$request = new Request;

$request->globalMenu();
?>

<div id="topright-trip">
  <div class="pagetitle">Create new trip</div>
</div>

<div id="contentright">
<?
if ($save == "Save")
{
	// FIRST ensure all abbrevs are valid
	$abbrev = strtok($abbreviations, " \n");
	while ($abbrev)
	{
		if (trim($abbrev) != "")
		{
			// check for valid species abbrev
			performCount("Verify abbreviation", "SELECT COUNT(*) FROM species WHERE Abbreviation='" . trim($abbrev) . "'") or die ("This is not a valid abbreviation " . $abbrev);
		}

		$abbrev = strtok(" \n");
	}

	// SECOND insert them
	$abbrev = strtok($abbreviations, " \n");
	while ($abbrev)
	{
		if (trim($abbrev) != "")
		{
			// insert this species
			$sightingID++;
			performQuery("Insert new sighting", "\nINSERT INTO sighting VALUES (" . $sightingID . ", '" . trim($abbrev) . "', '" . $locationName . "', '', '0', '0', '" . $tripDate . "');\n");
		}

		$abbrev = strtok(" \n");
	}

	echo "sightings inserted... ";

	$todayTripRecordCount = performCount("Trip recoprd already for today", "SELECT count(*) from trip WHERE Date='" . $tripDate . "';");

	if ($todayTripRecordCount == 0)
	{
		// FINALLY insert the trip record
		performQuery("Insert trip record", "INSERT INTO trip VALUES (" . ($tripID + 1) . ", '" . $leader . "', '', '" . $tripName . "', '" . $notes . "', '" . $tripDate . "');");
		echo "<a href=\"./tripdetail.php?tripid=" . ($tripID + 1) . "\">Trip Created</a>";
	}

}
?>

<form method="post" action="./tripcreate.php">

<table class="report-content" width=100%>
  <tr>
	<td class="fieldlabel">Leader</td>
	<td><input type="text" name="Leader" value="" size="20"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Trip Name</td>
	<td><input type="text" name="TripName" value="" size="20"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">TripDate</td>
	<td><input type="text" name="TripDate" value="<?= $dateString; ?>" size="20"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Location</td>
	<td>
	  <select name="LocationName">
        <?php while($info = mysql_fetch_array($locationList)) { echo "<option>" . $info["Name"] . "</option>\n"; } ?>
	  </select>
	</td>
  </tr>
  <tr>
	<td class="fieldlabel">Abbreviations</td>
	<td><textarea name="Abbreviations" cols="10" rows="20"></textarea></td>
  </tr>
  <tr>
	<td class="fieldlabel">Notes</td>
	<td><textarea name="Notes" cols="60" rows="20"></textarea></td>
  </tr>
  <tr>
	<td><input type="submit" name="Save" value="Save"/></td>
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
