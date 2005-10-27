
<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);
$orderInfo = $request->getOrderInfo();

htmlHead($orderInfo["LatinName"]);

$items[] = strtolower($orderInfo["LatinName"]);
$request->globalMenu();

$nextOrder = performCount("Find Next Order",
    "SELECT FLOOR(MIN(species.objectid) / POW(10,9)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid > " . ($request->getOrderID() + 1) * pow(10, 9) . " LIMIT 1");

if ($nextOrder != "")
{
	$nextOrderInfo = getOrderInfo($nextOrder * pow(10, 9));
	$nextOrderLinkText = $nextOrderInfo["LatinName"];
}
else
{
	$nextOrderLinkText = "";
}

$prevOrder = performCount("Find Previous Order",
    "SELECT FLOOR(MAX(species.objectid) / POW(10,9)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid < " . $request->getOrderID() * pow(10, 9) . " LIMIT 1");

if ($prevOrder != "")
{
	$prevOrderInfo = getOrderInfo($prevOrder * pow(10, 9));
	$prevOrderLinkText = $prevOrderInfo["LatinName"];
}
else
{
	$prevOrderLinkText = "";
}

?>

    <div class="topright">
	<? browseButtons("Order Detail", "./orderdetail.php?view=" . $request->getView() . "&orderid=", $request->getOrderID(),
					 $prevOrder, $prevOrderLinkText,
					 $nextOrder, $nextOrderLinkText); ?>

        <div class="pagetitle"><?= $orderInfo["LatinName"] ?></div>
        <div class="pagesubtitle"> <?= $orderInfo["CommonName"] ?></div>
	</div>

    <div class="contentright">
	  <div class="titleblock">


<?       $request->viewLinks("species"); ?>

      </div>

<?
$request->handleStandardViews();
footer();

?>

    </div>

<?
htmlFoot();
?>