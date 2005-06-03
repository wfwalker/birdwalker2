
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);
$orderInfo = $request->getOrderInfo();

htmlHead($orderInfo["LatinName"]);

globalMenu();
$items[] = strtolower($orderInfo["LatinName"]);
navTrailBirds($items);
?>

    <div class=contentright>
	<? browseButtons("Order Detail", "./orderdetail.php?view=" . $request->getView() . "&orderid=", $request->getOrderID(), 1, $request->getOrderID() - 1, $request->getOrderID() + 1, 22); ?>

	  <div class="titleblock">
        <div class=pagetitle><?= $orderInfo["CommonName"] ?></div>
        <div class=metadata> <?= $orderInfo["LatinName"] ?></div>
        <div class=metadata>
          locations:
			<?= $request->linkToSelfChangeView("locations", "list"); ?> |
			<?= $request->linkToSelfChangeView("locationsbymonth", "by month"); ?> |
			<?= $request->linkToSelfChangeView("locationsbyyear", "by year"); ?> |
			<?= $request->linkToSelfChangeView("map", "map"); ?><br/>
          species:	
            <a href="./orderdetail.php?view=species&orderid=<?= $request->getOrderID() ?>">list</a> |
	        <a href="./orderdetail.php?view=chrono&orderid=<?= $request->getOrderID() ?>">ABA</a> |
	        <a href="./orderdetail.php?view=speciesbymonth&orderid=<?= $request->getOrderID() ?>">by month</a> |
	        <a href="./orderdetail.php?view=speciesbyyear&orderid=<?= $request->getOrderID() ?>">by year</a><br/>
	    </div>
      </div>

<?
$request->handleStandardViews("species");
footer();

?>

    </div>

<?
htmlFoot();
?>