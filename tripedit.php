
<?php

require("./birdwalker.php");

getEnableEdit() or die("Editing disabled");

?>


<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $tripInfo["Name"] ?>,  <?php echo $tripInfo["niceDate"] ?></title>
</head>

<body>

<?php globalMenu(); disabledBrowseButtons(); navTrailTrips(); ?>

    <div class="navigationleft">
	  <a href="./tripedit.php?id=1">first</a>
	  <a href="./tripedit.php?id=<?php echo $_GET['id'] - 1 ?>">prev</a>
      <a href="./tripedit.php?id=<?php echo $_GET['id'] + 1 ?>">next</a>
      <a href="./tripedit.php?id=<?php echo $tripCount ?>">last</a>
    </div>

<div class="contentright">


<?php

$tripID = $_GET['id'];
$postTripID = $_POST['id'];
$save = $_POST['Save'];

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

<div class="titleblock">
  <a href="./tripdetail.php?id=<?php echo $tripInfo["objectid"] ?>">
    <div class=pagetitle><?php echo $tripInfo["Name"] ?></div>
  <div class=pagesubtitle><?php echo $tripInfo["niceDate"] ?></div>
</a>
</div>

<form method="post" action="./tripedit.php?id=<?php echo $tripID ?>">

<table class=report-content columns=2 width=100%>
  <tr>
	<td class=fieldlabel>Leader</td>
	<td><input type="text" name="Leader" value="<?php echo $tripInfo["Leader"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>ReferenceURL</td>
	<td><input type="text" name="ReferenceURL" value="<?php echo $tripInfo["ReferenceURL"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Name</td>
	<td><input type="text" name="Name" value="<?php echo $tripInfo["Name"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Notes</td>
	<td><textarea name="Notes" cols=60 rows=20><?php echo $tripInfo["Notes"] ?></textarea></td>
  </tr>

  <tr>
	<td class=fieldlabel>Date</td>
	<td><input type="text" name="Date" value="<?php echo $tripInfo["Date"] ?>" size=20/></td>
  </tr>
  <tr>
	<td><input type="hidden" name="id" value="<?php echo $tripID ?>"/></td>
	<td><input type="submit" name="Save" value="Save"/></td>
  </tr>
</table>

</form>

</div>
</body>
</html>
