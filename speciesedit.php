
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

array_key_exists('speciesid', $_GET) && $request->setSpeciesID($_GET['speciesid']);
array_key_exists('speciesid', $_POST) && $request->setSpeciesID($_POST['speciesid']);

$save = array_key_exists('Save', $_POST) ? $_POST['Save'] : "";

getEnableEdit() or die("Editing disabled");

if (($request->getSpeciesID() != "") && ($save == "Save"))
{
	$commonName = $_POST['CommonName'];
	$latinName = $_POST['LatinName'];
	$abbreviation = $_POST['Abbreviation'];
	$notes = $_POST['Notes'];
	$referenceURL = $_POST['ReferenceURL'];
	$abaCountable = $_POST['ABACountable'];

    performQuery("save changes to species", "update species set CommonName='" . $commonName . 
				 "', LatinName='" . $latinName . 
				 "', Abbreviation='" . $abbreviation . 
				 "', Notes='" . $notes . 
				 "', ReferenceURL='" . $referenceURL . 
				 "', ABACountable='" . $abaCountable . 
				 "' where objectid='" . $request->getSpeciesID() . "'");

	echo "<a href=\"./speciesdetail.php?speciesid=" . $request->getSpeciesID() . "\"><b>Species Updated</b></a>";
}

$speciesInfo = getSpeciesInfo($request->getSpeciesID());

htmlHead($speciesInfo["CommonName"]);

$request->globalMenu();
?>

<div id="topright-species">
    <? speciesBrowseButtons("./speciesedit.php", $request->getSpeciesID(), $request->getView()); ?>
  <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["objectid"] ?>">
    <div class="pagetitle"><?= $speciesInfo["CommonName"] ?></div>
</a>
</div>

<div id="contentright">

<form method="post" action="./speciesedit.php?speciesid=<?= $request->getSpeciesID() ?>">

<table class="report-content" width="100%">
  <tr>
	<td class="fieldlabel">Common Name</td>
	<td><input type="text" name="CommonName" value="<?= $speciesInfo["CommonName"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Latin Name</td>
	<td><input type="text" name="LatinName" value="<?= $speciesInfo["LatinName"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Abbreviation</td>
	<td><input type="text" name="Abbreviation" value="<?= $speciesInfo["Abbreviation"] ?>" size=30/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Notes</td>
	<td><textarea name="Notes" cols=60 rows=20><?= $speciesInfo["Notes"] ?></textarea></td>
  </tr>

  <tr>
	<td class="fieldlabel">ReferenceURL</td>
	<td><input type="text" name="ReferenceURL" value="<?= $speciesInfo["ReferenceURL"] ?>" size=80/></td>
  </tr>

  <tr>
	<td class="fieldlabel">ABACountable</td>
	<td><input type="text" name="ABACountable" value="<?= $speciesInfo["ABACountable"] ?>" size=20/></td>
  </tr>

  <tr>
	<td><input type="hidden" name="speciesid" value="<?= $request->getSpeciesID() ?>"/></td>
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
