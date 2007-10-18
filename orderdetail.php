<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

$speciesQuery = new SpeciesQuery($request);
$orderInfo = $request->getOrderInfo();

htmlHead($orderInfo["common_name"]);

$items[] = strtolower($orderInfo["common_name"]);
$request->globalMenu();

$nextOrder = performCount("Find Next Order",
    "SELECT FLOOR(MIN(species.id) / POW(10,9)) FROM species, sighting
      WHERE sightings.species_id=species.id
      AND species.id > " . ($request->getOrderID() + 1) * pow(10, 9) . " LIMIT 1");

if ($nextOrder != "")
{
	$nextOrderInfo = getOrderInfo($nextOrder * pow(10, 9));
	$nextOrderLinkText = $nextOrderInfo["common_name"];
}
else
{
	$nextOrderLinkText = "";
}

$prevOrder = performCount("Find Previous Order",
    "SELECT FLOOR(MAX(species.id) / POW(10,9)) FROM species, sighting
      WHERE sightings.species_id=species.id
      AND species.id < " . $request->getOrderID() * pow(10, 9) . " LIMIT 1");

if ($prevOrder != "")
{
	$prevOrderInfo = getOrderInfo($prevOrder * pow(10, 9));
	$prevOrderLinkText = $prevOrderInfo["common_name"];
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

        <div class="pagetitle"><?= $orderInfo["common_name"] ?></div>
        <div class="pagesubtitle"> <?= $orderInfo["latin_name"] ?></div>
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
