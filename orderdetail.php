
<?php

require("./birdwalker.php");

$orderid = $_GET["order"];

$whereClause = "species.Abbreviation = sighting.SpeciesAbbreviation and species.objectid >= " . $orderid * pow(10, 9) . " and species.objectid < " . ($orderid + 1) * pow(10, 9);

$orderQuery = getSpeciesQuery($whereClause);
$orderCount = mysql_num_rows($orderQuery);
$orderInfo = getOrderInfo($orderid * pow(10, 9));


?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?= $orderInfo["LatinName"] ?></title>
  </head>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
browseButtons("./orderdetail.php?order=", $orderid, 1, $orderid - 1, $orderid + 1, $orderCount);
$items[] = strtolower($orderInfo["LatinName"]);
navTrailBirds($items);
pageThumbnail("select sighting.*, rand() as shuffle from sighting, species where Photo='1' and " . $whereClause . " order by shuffle");
?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle><?= $orderInfo["CommonName"] ?></div>
        <div class=pagesubtitle> <?= $orderInfo["LatinName"] ?></div>
        <div class=metadata> <?= $orderCount ?> species</div>
      </div>

		
<?php

$divideByFamilys = ($orderCount > 20);
$prevFamilyNum = -1;

while($info = mysql_fetch_array($orderQuery))
{
	$familyNum =  floor($info["objectid"] / pow(10, 7));
	
	if ($divideByFamilys && ($prevFamilyNum != $familyNum))
    {
		$familyInfo = getFamilyInfo($familyNum * pow(10, 7));
?>
        <div class="heading"><i><?= $familyInfo["LatinName"] ?></i>, <?= $familyInfo["CommonName"] ?></div>
<?
    }
?>	

	<div class=firstcell><a href="./speciesdetail.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a></div>

<?
	$prevFamilyNum = $familyNum;
}
?>

    </div>
  </body>
</html>
