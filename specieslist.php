
<?php

require_once("./birdwalker.php");
require_once("./request.php");
require_once("./speciesquery.php");

$locationList = performQuery("Get All Locations", "select Name, objectid from location order by Name");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);

?>

<html>

<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
<title>birdWalker | Species List</title>
</head>

<body>

      <div class="pagetitle"><?= $speciesQuery->getPageTitle() ?></div>
	  <div class=heading><?= $speciesQuery->getSpeciesCount() ?> Species</div>

<?
	  if ($request->getView() == "")
	  {
		  $speciesQuery->formatTwoColumnSpeciesList();
	  }
	  else if ($request->getView() == "checklist")
	  { ?>
		  <form method="GET" action="./index.php">
		      <div><input type="text" value="Date"/></div>
              <div><select name="LocationName">
                 <?php while($info = mysql_fetch_array($locationList)) { echo "<option>" . $info["Name"] . "</option>\n"; } ?>
              </select></div>
		      <div><input type="submit" value="Submit"/></div>
<?
		  $dbQuery = $speciesQuery->performQuery();
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
<?	  }

      footer();
 ?>


<?
htmlFoot();
?>
