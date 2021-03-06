<?php

require_once("./birdwalker.php");
require_once("./request.php");

getEnableEdit() or die("Editing disabled");

$save = ""; array_key_exists("Save", $_POST) && $save = $_POST['Save'];
$abbreviations = ""; array_key_exists("Abbreviations", $_POST) && $abbreviations = $_POST['Abbreviations'];
$importedAbbreviations = ""; array_key_exists("importedAbbreviations", $_POST) && $importedAbbreviations = $_POST['importedAbbreviations'];
$notes = ""; array_key_exists("notes", $_POST) && $notes = $_POST['Notes'];
$locationID = ""; array_key_exists("LocationID", $_POST) && $locationName = $_POST['LocationID'];
$leader = ""; array_key_exists("Leader", $_POST) && $leader = $_POST['Leader'];
$tripDate = ""; array_key_exists("TripDate", $_POST) && $tripDate = $_POST['TripDate'];
$tripName = ""; array_key_exists("TripName", $_POST) && $tripName = $_POST['TripName'];

$locationList = performQuery("Get All Locations", "SELECT name, id FROM locations ORDER BY name");
$dateArray = getdate();
$dateString = $dateArray["year"] . "-" . $dateArray["mon"] . "-" .  $dateArray["mday"];
$sightingID = performCount("Get Highest Sighting ID", "SELECT MAX(id) from sightings;");
$tripID = performCount("Get Highest Trip ID", "SELECT MAX(id) from trips;");

htmlHead("Create a trip");

$request = new Request;

$request->globalMenu();

$speciesQuery = new SpeciesQuery($request);

?>

<div id="topright-trip">
  <div class="pagetitle">Create new trip</div>
</div>

<div id="contentright">
<?
if ($save == "Save")
{
	$todayTripRecordCount = performCount("Trip record already for today", "SELECT count(*) from trips WHERE date='" . $tripDate . "';");

	if ($todayTripRecordCount == 0)
	{
		// FINALLY insert the trip record
		performQuery("Insert trip record",
					 "INSERT INTO trip VALUES (" . ($tripID + 1) . ", '" . $leader . "', '', '" . $tripName . "', '" . $notes . "', '" . $tripDate . "');");
		echo "<a href=\"./tripdetail.php?tripid=" . ($tripID + 1) . "\">Trip Created</a>";
	}
	
    // validate abbreviations imported from a text memo. FIRST verify them
    $importedAbbrev = strtok($importedAbbreviations, " \n");
    while ($importedAbbrev)
    {
	  if (trim($importedAbbrev) != "")
		{
		  // check for valid species abbrev
		  performCount("Verify abbreviation",
					   "SELECT COUNT(*) FROM species WHERE abbbreviation='" . trim($importedAbbrev) . "'")
			or die ("This is not a valid abbreviation " . $importedAbbrev);
		}

	  $importedAbbrev = strtok(" \n");
	}
	
	// actually insert abbreviations imported from a text memo
	$importedAbbrev = strtok($importedAbbreviations, " \n");
	while ($importedAbbrev)
	{
		if (trim($importedAbbrev) != "")
		{
			// insert this species
			$sightingID++;
			
			// check for valid species abbrev
		    $species_id = performCount("Lookup ID for species", "SELECT id FROM species WHERE abbbreviation='" . trim($importedAbbrev) . "'");
		  
			performQuery("Insert new sighting",
						 "\nINSERT INTO sightings VALUES (" . $sightingID . ", '" .
						 $species_id . "', '" . $locationID . "', '', '0', '0', '" . $tripID . "');\n");
		}

	    $importedAbbrev = strtok(" \n");
	}

	if ($abbreviations != "") 
    {
	    // insert abbreviations from checkboxes
	    foreach ($abbreviations as $abbrev)
		{
		  if (trim($abbrev) != "")
			{
			  // insert this species
			  $sightingID++;
			  performQuery("Insert new sighting",
						   "\nINSERT INTO sighting VALUES (" . $sightingID . ", '" .
						   trim($abbrev) . "', '" . $locationName . "', '', '0', '0', '" . $tripDate . "');\n");
			}
		}
	}

	echo "sightings inserted... ";
}
?>

<form method="post" action="./tripcreate.php">

<table class="report-content" width="100%">
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
	  <select name="LocationID">
<?        while($info = mysql_fetch_array($locationList))
	      { ?>
		      <option value="<?= $info['id'] ?>" <?= ($request->getLocationID()==$info["id"] ? "selected" : "") ?>>
                  <?= $info["name"]  ?>
              </option>
<?		  } ?>
	  </select>
	</td>
  </tr>
  <tr>
	<td class="fieldlabel">Notes</td>
	<td><textarea name="notes" cols="60" rows="20"></textarea></td>
  </tr>

  <tr><td class="fieldlabel"></td><td>
<?
		  $dbQuery = $speciesQuery->performQuery();
		  $divideByTaxo = mysql_num_rows($dbQuery) > 30;
          $prevInfo = ""; 

		  while($info = mysql_fetch_array($dbQuery))
		  {
			  if ($prevInfo == "" || ($divideByTaxo && (getFamilyInfo($prevInfo["id"]) != getFamilyInfo($info["id"]))))
			  {
				  $taxoInfo = getFamilyInfo($info["id"]); ?>
				  <div class="subheading"><?= strtolower($taxoInfo["latin_name"]) ?></div>
<?            } ?>

			  <div>
                  <input type="checkbox" name="Abbreviations[]" value="<?= $info["abbreviation"]?>"/>
                  <?= $info["common_name"] ?>
              </div>
<?            $prevInfo = $info;
          }
?>

  </td></tr>

  <tr>
     <td class="fieldlabel">Abbreviations</td>
     <td><textarea name="importedAbbreviations" cols="10" rows="20"></textarea></td>
  </tr>
			
  <tr>
	<td></td>
    <td><input type="submit" name="Save" value="Save"/></td>
  </tr>



</table>
</form>
</div>

<?
htmlFoot();
?>
