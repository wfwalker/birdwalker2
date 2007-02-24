<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");

$locationList = performQuery("Get All Locations", "select Name, objectid from location order by Name");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);

htmlHead("thing");
$request->globalMenu();

?>


  <div id="topright-species">
  </div>

  <div id="contentright">
	

<?
		  $dbQuery = $speciesQuery->performQuery();
		  $divideByTaxo = mysql_num_rows($dbQuery) > 20;
          $prevInfo = ""; 

		  while($info = mysql_fetch_array($dbQuery))
		  {
			  if ($divideByTaxo && ($prevInfo == "" || getFamilyInfo($prevInfo["objectid"]) != getFamilyInfo($info["objectid"])))
			  {
				  $taxoInfo = getFamilyInfo($info["objectid"]); ?>
				  <div class="subheading"><?= strtolower($taxoInfo["LatinName"]) ?></div>
<?            } ?>

			  <div>
                  <input type="checkbox" name="Abbreviations" value="<?= $info["Abbreviation"]?>"/>
                  <?= $info["CommonName"] ?>
              </div>
<?            $prevInfo = $info;
          }
 ?>

  </div>

<?
htmlFoot();
?>
