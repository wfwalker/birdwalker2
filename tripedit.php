<?php

require_once("./birdwalker.php");
require_once("./request.php");

$tripID = getValue('tripid');
$postTripID = postValue('tripid');
$save = postValue('Save');

getEnableEdit() or die("Editing disabled");

if (($postTripID != "") && ($save == "Save"))
{
    $postTripInfo = getTripInfo($postTripID);

	$leader = postValue('Leader');
	$referenceURL = postValue('ReferenceURL');
	$date = postValue('Date');
	$notes = postValue('Notes');
	$name = postValue('Name');

    if ($date != $postTripInfo['Date'])
	{
	    $alreadyHasNewDate = performCount("already dated that", "SELECT COUNT(*) FROM trip WHERE Date='" . mysql_escape_string($date) . "'");
	    if ($alreadyHasNewDate != "0") { die("Trip date '" . $date . "' already in use."); }

		performQuery("Update sightings", "UPDATE sighting SET ".
					 "TripDate='" . $date . "' WHERE TripDate='" . mysql_escape_string($postTripInfo['Date']) . "'");

		echo "trip redated";
	}

	performQuery("Update trip", "update trip set Leader='" . $leader . 
				 "', ReferenceURL='" . $referenceURL . 
				 "', Name='" . $name . 
				 "', Date='" . $date . 
				 "', Notes='" . $notes . 
				 "' where id='" . $postTripID . "'");

	$tripID = $postTripID;

	echo "<b>Trip Updated</b>";
}

$request = new Request;
$tripInfo = $request->getTripInfo();

htmlHead($tripInfo["Name"] . ", " .$tripInfo["niceDate"]);

$request->globalMenu();

?>

<div id="topright-trip">
  <? tripBrowseButtons("./tripedit.php", $tripInfo, "edit"); ?>
  <a href="./tripdetail.php?tripid=<?= $tripInfo["id"] ?>">
    <div class="pagetitle"><?= $tripInfo["Name"] ?></div>
    <div class="pagesubtitle"><?= $tripInfo["niceDate"] ?></div>
</a>
</div>

<div id="contentright">

<form method="post" action="./tripedit.php?tripid=<?= $tripID ?>">

<table class="report-content" width="100%">
  <tr>
	<td class="fieldlabel">Leader</td>
	<td><input type="text" name="Leader" value="<?= $tripInfo["Leader"] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">ReferenceURL</td>
	<td><input type="text" name="ReferenceURL" value="<?= $tripInfo["ReferenceURL"] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Name</td>
	<td><input type="text" name="Name" value="<?= $tripInfo["Name"] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Notes</td>
    <td><textarea name="Notes" cols="60" rows="20"><?= stripslashes($tripInfo["Notes"]) ?></textarea></td>
  </tr>

  <tr>
	<td class="fieldlabel">Date</td>
	<td><input type="text" name="Date" value="<?= $tripInfo["Date"] ?>" size="20"/></td>
  </tr>
  <tr>
	<td><input type="hidden" name="tripid" value="<?= $tripID ?>"/></td>
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
