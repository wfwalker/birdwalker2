
<?php

require("./birdwalker.php");

getEnableEdit() or die("Editing disabled");

$save = $_POST['Save'];
$abbreviations = $_POST['Abbreviations'];
$notes = $_POST['Notes'];
$locationName = $_POST['LocationName'];
$leader = $_POST['Leader'];
$tripDate = $_POST['TripDate'];
$tripName = $_POST['TripName'];

$locationList = performQuery("select Name, objectid from location order by Name");
$dateArray = getdate();
$dateString = $dateArray["year"] . "-" . $dateArray["mon"] . "-" .  $dateArray["mday"];
$sightingID = performCount("select max(objectid) from sighting;");
$tripID = performCount("select max(objectid) from trip;");

?>

<html>

  <? htmlHead("Create a trip"); ?>

  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailTrips(); ?>

<div class="contentright">
<div class="titleblock">
	  <div class=pagetitle>Create new trip</div>
</div>

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
			performCount("select count(*) from species where Abbreviation='" . trim($abbrev) . "'") or die ("This is not a valid abbreviation " . $abbrev);
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
			performQuery("\nINSERT INTO sighting VALUES (" . $sightingID . ", '" . trim($abbrev) . "', '" . $locationName . "', '', '0', '0', '" . $tripDate . "');\n");
		}

		$abbrev = strtok(" \n");
	}

	// FINALLY insert the trip record
	performQuery("INSERT INTO trip VALUES (" . ($tripID + 1) . ", '" . $leader . "', '', '" . $tripName . "', '" . $notes . "', '" . $tripDate . "');");

	echo "<a href=\"./tripdetail.php?tripid=" . ($tripID + 1) . "\">Trip Created</a>";
}
?>

<form method="post" action="./tripcreate.php">

<table class=report-content columns=2 width=100%>
  <tr>
	<td class=fieldlabel>Leader</td>
	<td><input type="text" name="Leader" value="" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Trip Name</td>
	<td><input type="text" name="TripName" value="" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>TripDate</td>
	<td><input type="text" name="TripDate" value="<?= $dateString; ?>" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Location</td>
	<td>
	  <select name="LocationName">
        <?php while($info = mysql_fetch_array($locationList)) { echo "<option>" . $info["Name"] . "</option>\n"; } ?>
	  </select>
	</td>
  </tr>
  <tr>
	<td class=fieldlabel>Abbreviations</td>
	<td><textarea name="Abbreviations" cols=10 rows=20></textarea></td>
  </tr>
  <tr>
	<td class=fieldlabel>Notes</td>
	<td><textarea name="Notes" cols=60 rows=20></textarea></td>
  </tr>
  <tr>
	<td><input type="submit" name="Save" value="Save"/></td>
  </tr>
</table>

</form>

</div>
</body>
</html>
