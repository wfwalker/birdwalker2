
<?php

require("./birdwalker.php");

$speciesID = $_GET['id'];
$postSpeciesID = $_POST['id'];
$save = $_POST['Save'];

getEnableEdit() or die("Editing disabled");

?>


<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $speciesInfo["Name"] ?>,  <?= $speciesInfo["niceDate"] ?></title>
</head>

<body>

<?php
globalMenu();
speciesBrowseButtons($speciesID, "edit");
navTrailSpecies($speciesID);
?>

<div class="contentright">

<?php

if (($postSpeciesID != "") && ($save == "Save"))
{
	$commonName = $_POST['CommonName'];
	$latinName = $_POST['LatinName'];
	$abbreviation = $_POST['Abbreviation'];
	$notes = $_POST['Notes'];
	$referenceURL = $_POST['ReferenceURL'];
	$abaCountable = $_POST['ABACountable'];

	performQuery("update species set CommonName='" . $commonName . 
				 "', latinName='" . $latinName . 
				 "', Abbreviation='" . $abbreviation . 
				 "', Notes='" . $notes . 
				 "', ReferenceURL='" . $referenceURL . 
				 "', ABACountable='" . $abaCountable . 
				 "' where objectid='" . $postSpeciesID . "'");

	$speciesID = $postSpeciesID;

	echo "<b>Species Updated</b>";
}

$speciesInfo = getSpeciesInfo($speciesID);
?>

<div class=pagesubtitle><?= $speciesInfo["niceDate"] ?></div>
<div class="titleblock">
  <a href="./speciesdetail.php?id=<?= $speciesInfo["objectid"] ?>">
    <div class=pagetitle><?= $speciesInfo["Name"] ?></div>
</a>
</div>

<form method="post" action="./speciesedit.php?id=<?= $speciesID ?>">

<table class=report-content columns=2 width=100%>
  <tr>
	<td class=fieldlabel>Common Name</td>
	<td><input type="text" name="CommonName" value="<?= $speciesInfo["CommonName"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Latin Name</td>
	<td><input type="text" name="Latin Name" value="<?= $speciesInfo["LatinName"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Abbreviation</td>
	<td><input type="text" name="Abbreviation" value="<?= $speciesInfo["Abbreviation"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class=fieldlabel>Notes</td>
	<td><textarea name="Notes" cols=60 rows=20><?= $speciesInfo["Notes"] ?></textarea></td>
  </tr>

  <tr>
	<td class=fieldlabel>ReferenceURL</td>
	<td><input type="text" name="ReferenceURL" value="<?= $speciesInfo["ReferenceURL"] ?>" size=80/></td>
  </tr>

  <tr>
	<td class=fieldlabel>ABACountable</td>
	<td><input type="text" name="ABACountable" value="<?= $speciesInfo["ABACountable"] ?>" size=20/></td>
  </tr>

  <tr>
	<td><input type="hidden" name="id" value="<?= $speciesID ?>"/></td>
	<td><input type="submit" name="Save" value="Save"/></td>
  </tr>
</table>

</form>

</div>
</body>
</html>