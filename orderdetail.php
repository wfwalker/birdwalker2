
<?php

require_once("./birdwalker.php");
require_once("./speciesquery.php");

$orderid = param($_GET, "order", 21);

$speciesQuery = new SpeciesQuery;
$speciesQuery->setOrder($orderid);

$orderInfo = getOrderInfo($orderid * pow(10, 9));

?>

<html>

  <? htmlHead($orderInfo["LatinName"]); ?>

  <body>

<?php
globalMenu();
disabledBrowseButtons();
browseButtons("./orderdetail.php?order=", $orderid, 1, $orderid - 1, $orderid + 1, $orderCount);
$items[] = strtolower($orderInfo["LatinName"]);
navTrailBirds($items);
?>

    <div class=contentright>
      <div class=pagesubtitle>Order Detail</div>
	  <div class="titleblock">
        <div class=pagetitle><?= $orderInfo["CommonName"] ?></div>
        <div class=metadata> <?= $orderInfo["LatinName"] ?></div>
      </div>

<div class=heading><?= $speciesQuery->getSpeciesCount() ?> species</div>

<? formatSpeciesListWithPhoto($speciesQuery); ?>

    </div>
  </body>
</html>
