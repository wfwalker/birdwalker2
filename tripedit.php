
<?php

require("./birdwalker.php");

$tripID = $_GET['tripid'];
$postTripID = $_POST['tripid'];
$save = $_POST['Save'];

getEnableEdit() or die("Editing disabled");

?>


<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $tripInfo["Name"] ?>,  <?= $tripInfo["niceDate"] ?></title>
</head>

<body>

<?php
globalMenu();
tripBrowseButtons("./tripedit.php", $tripID, "edit");
navTrailTrips();
?>

<div class="contentright">

<?php

if (($postTripID != "") && ($save == "Save"))
{
	$leader = $_POST['Leader'];
	$referenceURL = $_POST['ReferenceURL'];
	$date = $_POST['Date'];
	$notes = $_POST['Notes'];
	$name = $_POST['Name'];

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
?>

<div class=pagesubtitle><?= $tripInfo["niceDate"] ?></div>
<div class="titleblock">
  <a href="./tripdetail.php?tripid=<?= $tripInfo["objectid"] ?>">
    <div class=pagetitle><?= $tripInfo["Name"] ?></div>
</a>
</div>

<form method="post" action="./tripedit.php?tripid=<?= $tripID ?>">

<table class=report-content columns=2 width=100%>
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

</div>
</body>
</html>
