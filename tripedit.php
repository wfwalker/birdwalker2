
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$tripID = getValue('tripid');
$postTripID = postValue('tripid');
$save = postValue('Save');

getEnableEdit() or die("Editing disabled");

if (($postTripID != "") && ($save == "Save"))
{
	$leader = postValue('Leader');
	$referenceURL = postValue('ReferenceURL');
	$date = postValue('Date');
	$notes = postValue('Notes');
	$name = postValue('Name');

	performQuery("update trip set Leader='" . $leader . 
				 "', ReferenceURL='" . $referenceURL . 
				 "', Name='" . $name . 
				 "', Date='" . $date . 
				 "', Notes='" . $notes . 
				 "' where objectid='" . $postTripID . "'");

	$tripID = $postTripID;

	echo "<b>Trip Updated</b>";
}

$tripInfo = getTripInfo($tripID);

htmlHead($tripInfo["Name"] . ", " .$tripInfo["niceDate"]);

$request = new Request;

$request->globalMenu();

?>

<div class="contentright">

<? tripBrowseButtons("./tripedit.php", $tripID, "edit"); ?>
<div class="titleblock">
  <a href="./tripdetail.php?tripid=<?= $tripInfo["objectid"] ?>">
    <div class=pagetitle><?= $tripInfo["Name"] ?></div>
</a>
<div class=metadata><?= $tripInfo["niceDate"] ?></div>
</div>

<form method="post" action="./tripedit.php?tripid=<?= $tripID ?>">

<table class=report-content width=100%>
  <tr>
	<td class=fieldlabel>Leader</td>
	<td><input type="text" name="Leader" value="<?= $tripInfo["Leader"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>ReferenceURL</td>
	<td><input type="text" name="ReferenceURL" value="<?= $tripInfo["ReferenceURL"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Name</td>
	<td><input type="text" name="Name" value="<?= $tripInfo["Name"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Notes</td>
	<td><textarea name="Notes" cols=60 rows=20><?= $tripInfo["Notes"] ?></textarea></td>
  </tr>

  <tr>
	<td class=fieldlabel>Date</td>
	<td><input type="text" name="Date" value="<?= $tripInfo["Date"] ?>" size=20/></td>
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
