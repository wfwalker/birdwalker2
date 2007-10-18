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
	$common_name = $_POST['common_name'];
	$latin_name = $_POST['latin_name'];
	$abbreviation = $_POST['Abbreviation'];
	$notes = $_POST['Notes'];
	$reference_url = $_POST['reference_url'];
	$aba_countable = $_POST['aba_countable'];

    performQuery("save changes to species", "update species set common_name='" . $common_name . 
				 "', latin_name='" . $latin_name . 
				 "', Abbreviation='" . $abbreviation . 
				 "', Notes='" . $notes . 
				 "', reference_url='" . $reference_url . 
				 "', aba_countable='" . $aba_countable . 
				 "' where id='" . $request->getSpeciesID() . "'");

	echo "<a href=\"./speciesdetail.php?speciesid=" . $request->getSpeciesID() . "\"><b>Species Updated</b></a>";
}

$speciesInfo = getSpeciesInfo($request->getSpeciesID());

htmlHead($speciesInfo["common_name"]);

$request->globalMenu();
?>

<div id="topright-species">
    <? speciesBrowseButtons("./speciesedit.php", $request->getSpeciesID(), $request->getView()); ?>
  <a href="./speciesdetail.php?speciesid=<?= $speciesInfo["id"] ?>">
    <div class="pagetitle"><?= $speciesInfo["common_name"] ?></div>
</a>
</div>

<div id="contentright">

<form method="post" action="./speciesedit.php?speciesid=<?= $request->getSpeciesID() ?>">

<table class="report-content" width="100%">
  <tr>
	<td class="fieldlabel">Common Name</td>
	<td><input type="text" name="common_name" value="<?= $speciesInfo["common_name"] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Latin Name</td>
	<td><input type="text" name="latin_name" value="<?= $speciesInfo["latin_name"] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Abbreviation</td>
	<td><input type="text" name="Abbreviation" value="<?= $speciesInfo["Abbreviation"] ?>" size="30"/></td>
  </tr>
  <tr>
	<td class="fieldlabel">Notes</td>
    <td><textarea name="notes" cols="60" rows="20"><?= stripslashes($speciesInfo["notes"]) ?></textarea></td>
  </tr>

  <tr>
	<td class="fieldlabel">reference_url</td>
	<td><input type="text" name="reference_url" value="<?= $speciesInfo["reference_url"] ?>" size="80"/></td>
  </tr>

  <tr>
	<td class="fieldlabel">aba_countable</td>
	<td><input type="text" name="aba_countable" value="<?= $speciesInfo["aba_countable"] ?>" size="20"/></td>
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
