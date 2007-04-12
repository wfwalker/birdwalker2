<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);
$orderInfo = $request->getOrderInfo();

htmlHead($orderInfo["CommonName"]);

$items[] = strtolower($orderInfo["CommonName"]);
$request->globalMenu();

$nextOrder = performCount("Find Next Order",
    "SELECT FLOOR(MIN(species.objectid) / POW(10,9)) FROM species, sighting
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND species.objectid > " . ($request->getOrderID() + 1) * pow(10, 9) . " LIMIT 1");

if ($nextOrder != "")
{
	$nextOrderInfo = getOrderInfo($nextOrder * pow(10, 9));
	$nextOrderLinkText = $nextOrderInfo["CommonName"];
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
	$prevOrderLinkText = $prevOrderInfo["CommonName"];
}
else
{
	$prevOrderLinkText = "";
}

?>

    <div id="topright-species">
	<? browseButtons("Order Detail", "./orderdetail.php?view=" . $request->getView() . "&orderid=", $request->getOrderID(),
					 $prevOrder, $prevOrderLinkText,
					 $nextOrder, $nextOrderLinkText); ?>

        <div class="pagetitle"><?= $orderInfo["CommonName"] ?></div>
        <div class="pagesubtitle"> <?= $orderInfo["LatinName"] ?></div>
<?       $request->viewLinks("species"); ?>
	</div>

    <div id="contentright">
<?
$request->handleStandardViews();
footer();

?>

    </div>

<?
htmlFoot();
?>
