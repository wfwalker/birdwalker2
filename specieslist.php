
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");

$locationList = performQuery("select Name, objectid from location order by Name");

$aQuery = new SpeciesQuery;
$aQuery->setFromRequest($_GET);

$view = param($_GET, "view", "list");

?>

<html>

  <? htmlHead($aQuery->getPageTitle()); ?>

  <body>

<?
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

    <div class=contentright>
      <div class="titleblock">
<?    $aQuery->rightThumbnail() ?>
      <div class=pagetitle><?= $aQuery->getPageTitle() ?></div>
<?    if (($state == 'CA') && ($year != "")) { ?><div class=metadata>See also our <a href="./chronocayearlist.php?year=<?=$year?>">California ABA Year List for <?=$year?></a></div><? } ?>
      </div>

	  <div class=heading><?= $aQuery->getSpeciesCount() ?> Species</div>

<?
	  if ($view == "list")
	  {
		  $aQuery->formatTwoColumnSpeciesList();
	  }
	  else if ($view == "checklist")
	  { ?>
		  <form method="GET" action="./index.php">
		      <div><input type="text" value="Date"/></div>
              <div><select name="LocationName">
                 <?php while($info = mysql_fetch_array($locationList)) { echo "<option>" . $info["Name"] . "</option>\n"; } ?>
              </select></div>
		      <div><input type="submit" value="Submit"/></div>
<?
		  $dbQuery = $aQuery->performQuery();
		  $divideByTaxo = mysql_num_rows($dbQuery) > 20;

		  while($info = mysql_fetch_array($dbQuery))
		  {
			  if ($divideByTaxo && (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"])))
			  {
				  $taxoInfo = getBestTaxonomyInfo($info["objectid"]); ?>
				  <div class=subheading><?= strtolower($taxoInfo["LatinName"]) ?></div>
<?            } ?>

			  <div>
                  <input type="checkbox" name="Abbreviations" value="<?= $info["Abbreviation"]?>"/>
                  <?= $info["CommonName"] ?>
              </div>
<?            $prevInfo = $info;
          } ?>
		  </form>
<?	  } ?>

    </div>
  </body>
</html>