
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

$locationList = performQuery("select Name, objectid from location");
$dateArray = getdate();
$dateString = $dateArray["year"] . "-" . $dateArray["mon"] . "-" .  $dateArray["mday"];
$sightingCount = performCount("select count(*) from sighting;");
$tripCount = performCount("select count(*) from trip;");

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | Create a trip</title>
</head>

<body>

<?php navigationHeader() ?>

<div class="contentright">
<div class="titleblock">
	  <div class=pagetitle>Create new trip</div>
</div>

<?
if ($save == "Save")
{
	performQuery("INSERT INTO trip VALUES (" . ($tripCount + 1) . ", '" . $leader . "', '', '" . $tripName . "', '" . $notes . "', '" . $tripDate . "');");

	$abbrev = strtok($abbreviations, " \n");
	while ($abbrev)
	{
		$sightingCount++;
		performQuery("\nINSERT INTO sighting VALUES (" . $sightingCount . ", '" . trim($abbrev) . "', '" . $locationName . "', '', '0', '0', '" . $tripDate . "');\n");
		$abbrev = strtok(" \n");
	}

	echo "<a href=\"./tripdetail.php?id=" . ($tripCount + 1) . "\">Trip Created</a>";
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
	<td><input type="text" name="TripDate" value="<?php echo $dateString; ?>" size=20/></td>
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