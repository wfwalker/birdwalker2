
<?php

require("./birdwalker.php");

getEnableEdit() or die("Editing disabled");

$sightingCount = performCount("select max(objectid) from sighting");

// the GET id determines which record to show
$sightingID = $_GET['id'];

// the POST id determines which record to update
$postSightingID = $_POST['id'];

// The SAVE and NEW buttons determine whether to update or createa a new record
$save = $_POST['Save'];
$new = $_POST['New'];

// if NEW, set the POST id to a new unique sighting objectid
if ($new == "New") { $postSightingID = 1 + performCount("select max(objectid) from sighting"); }

// if we have a POST id and either a new or a save button, then time to update
if ($postSightingID != "") {
	$speciesAbbreviation = $_POST['SpeciesAbbreviation'];
	$locationName = $_POST['LocationName'];
	$tripDate = $_POST['TripDate'];
	$notes = $_POST['Notes'];
	$exclude = $_POST['Exclude'];
	$photo = $_POST['Photo'];

	if ($save != "") {
		performQuery("update sighting set SpeciesAbbreviation='" . $speciesAbbreviation . 
					 "', LocationName='" . $locationName . 
					 "', TripDate='" . $tripDate . 
					 "', Notes='" . $notes . 
					 "', Photo='" . $photo . 
					 "', Exclude='" . $exclude . 
					 "' where objectid='" . $postSightingID . "'");
	} else if ($new != "") {
		performQuery("insert into sighting values (". $postSightingID .
					 ", '" . $speciesAbbreviation . 
					 "', '" . $locationName . 
					 "', '" . $notes . 
					 "', '" . $exclude . 
					 "', '" . $photo . 
					 "', '" . $tripDate . "');");
	}

	$sightingID = $postSightingID;			   
}

$sightingInfo = getSightingInfo($sightingID);
$speciesInfo = performOneRowQuery("select * from species where Abbreviation='" . $sightingInfo["SpeciesAbbreviation"] . "'");
$tripInfo = performOneRowQuery("select *, date_format(Date, '%W,  %M %e, %Y') as niceDate from trip where Date='" . $sightingInfo["TripDate"] . "'");
$locationInfo = performOneRowQuery("select * from location where Name='" . $sightingInfo["LocationName"] . "'");
$locationList = performQuery("select Name, objectid from location");
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $speciesInfo["CommonName"] ?>,  <?php echo $tripInfo["niceDate"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

    <div class="navigationleft">
	  <a href="./sightingedit.php?id=1">first</a>
	  <a href="./sightingedit.php?id=<?php echo $_GET['id'] - 1 ?>">prev</a>
      <a href="./sightingedit.php?id=<?php echo $_GET['id'] + 1 ?>">next</a>
      <a href="./sightingedit.php?id=<?php echo $sightingCount ?>">last</a>
    </div>

<div class="contentright">

<div class="titleblock">

<?php if ($sightingInfo["Photo"] == "1") { echo getThumbForSightingInfo($sightingInfo); } ?>

  <div class=pagetitle>
    <a href="./speciesdetail.php?id=<?php echo $speciesInfo["objectid"] ?>"><?php echo $speciesInfo["CommonName"] ?></a>
  </div>
  <div class=pagesubtitle>
    <a href="./tripdetail.php?id=<?php echo $tripInfo["objectid"] ?>"><?php echo $tripInfo["niceDate"] ?>
  </div>
  <div class=metadata>
    <a href="./countydetail.php?county=<?php echo $locationInfo["County"] ?>"><?php echo $locationInfo["County"] ?> County</a>,
    <a href="./statedetail.php?state=<?php echo $locationInfo["State"] ?>"><?php echo getStateNameForAbbreviation($locationInfo["State"]) ?></a>
  </div>
</div>

<form method="post" action="./sightingedit.php?id=<?php echo $sightingID ?>">

<table class=report-content columns=2 width=100%>
  <tr>
	<td class=fieldlabel>Abbreviation</td>
	<td><input type="text" name="SpeciesAbbreviation" value="<?php echo $sightingInfo["SpeciesAbbreviation"] ?>" size=6/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Location</td>
	<td>
	  <select name="LocationName">
<?php
   while($info = mysql_fetch_array($locationList))
   {
	   if ($info["Name"] == $sightingInfo["LocationName"]) { echo "<option selected>"; } else { echo "<option>"; }
	   echo $info["Name"] . "</option>\n";
   }
?>
	  </select>
	</td>
  </tr>
  <tr>
	<td class=fieldlabel>Notes</td>
	<td><textarea name="Notes" cols=60 rows=20><?php echo $sightingInfo["Notes"] ?></textarea></td>
  </tr>
  <tr>
	<td class=fieldlabel>Exclude</td>
	<td><input type="checkbox" name="Exclude" value="1" <?php if ($sightingInfo["Exclude"] == "1") { echo "checked"; } ?> /></td>
  </tr>
  <tr>
	<td class=fieldlabel>Photo</td>
	<td><input type="checkbox" name="Photo" value="1" <?php if ($sightingInfo["Photo"] == "1") { echo "checked"; } ?> /></td>
  </tr>
  <tr>
	<td class=fieldlabel>TripDate</td>
	<td><input type="text" name="TripDate" value="<?php echo $sightingInfo["TripDate"] ?>" size=20/></td>
  </tr>
  <tr>
	<td><input type="hidden" name="id" value="<?php echo $sightingID ?>"/></td>
	<td><input type="submit" name="Save" value="Save"/><input type="submit" name="New" value="New"/></td>
  </tr>
</table>

</form>

</div>
</body>
</html>
