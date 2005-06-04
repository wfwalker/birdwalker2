
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);
$orderInfo = $request->getOrderInfo();

htmlHead($orderInfo["LatinName"]);

globalMenu();
$items[] = strtolower($orderInfo["LatinName"]);
$request->navTrailBirds();
?>

    <div class=contentright>
	<? browseButtons("Order Detail", "./orderdetail.php?view=" . $request->getView() . "&orderid=", $request->getOrderID(), 1, $request->getOrderID() - 1, $request->getOrderID() + 1, 22); ?>

	  <div class="titleblock">
        <div class=pagetitle><?= $orderInfo["CommonName"] ?></div>
        <div class=metadata> <?= $orderInfo["LatinName"] ?></div>


<?       $request->viewLinks(); ?>

      </div>

<?
$request->handleStandardViews("species");
footer();

?>

    </div>

<?
htmlFoot();
?>