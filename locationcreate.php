
<?php

require("./birdwalker.php");

getEnableEdit() or die("Editing disabled");

// the GET id determines which record to show
$locationID = $_GET['id'];

// the POST id determines which record to update
$postLocationID = $_POST['id'];

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

?>

<html>

<head>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $locationInfo["Name"] ?>,  <?php echo $locationInfo["State"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

    <div class="navigationleft">
	  <a href="./locationcreate.php?id=1">first</a>
	  <a href="./locationcreate.php?id=<?php echo $_GET['id'] - 1 ?>">prev</a>
      <a href="./locationcreate.php?id=<?php echo $_GET['id'] + 1 ?>">next</a>
      <a href="./locationcreate.php?id=<?php echo $locationCount ?>">last</a>
    </div>

<div class="contentright">
<div class="titleblock">
  <a href="./locationdetail.php?id=<?php echo $locationInfo["objectid"] ?>">
    <div class=pagetitle><?php echo $locationInfo["Name"] ?></div>
  <div class=pagesubtitle><?php echo $locationInfo["niceDate"] ?></div>
</a>
</div>

<?
?>

<form method="post" action="./locationcreate.php?id=<?php echo $locationID ?>">

<table class=report-content columns=2 width=100%>
  <tr>
	<td class=fieldlabel>Name</td>
	<td><input type="text" name="Name" value="<?php echo $locationInfo['Name'] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>ReferenceURL</td>
	<td><input type="text" name="ReferenceURL" value="<?php echo $locationInfo['ReferenceURL'] ?>" size=60/></td>
  </tr>
  <tr>
	<td class=fieldlabel>City</td>
	<td><input type="text" name="City" value="<?php echo $locationInfo['City'] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>County</td>
	<td><input type="text" name="County" value="<?php echo $locationInfo['County'] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>State</td>
	<td><input type="text" name="State" value="<?php echo $locationInfo['State'] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>LatLongSystem</td>
	<td><input type="text" name="LatLongSystem" value="<?php echo $locationInfo['LatLongSystem'] ?>" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Latitude</td>
	<td><input type="text" name="Latitude" value="<?php echo $locationInfo['Latitude'] ?>" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Longitude</td>
	<td><input type="text" name="Longitude" value="<?php echo $locationInfo['Longitude'] ?>" size=20/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Notes</td>
	<td><textarea name="Notes" cols=60 rows=20><?php echo $locationInfo['Notes'] ?></textarea></td>
  </tr>
  <tr>
	<td><input type="hidden" name="id" value="<?php echo $locationID ?>"/></td>
	<td><input type="submit" name="Save" value="Save"/><input type="submit" name="New" value="New"/></td>
  </tr>
</table>

</form>

</div>
</body>
</html>
