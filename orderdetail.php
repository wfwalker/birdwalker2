
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
?>

    <div class=contentright>
      <div class=pagesubtitle> <?= $orderInfo["LatinName"] ?></div>
	  <div class="titleblock">
        <div class=pagetitle><?= $orderInfo["CommonName"] ?></div>
      </div>


     <div class=heading> <?= $orderCount ?> species</div>
		
<?php

	formatTwoColumnSpeciesList($orderQuery);
?>

    </div>
  </body>
</html>
