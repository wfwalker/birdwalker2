
<?php

require("./birdwalker.php");
require("./speciesquery.php");

$orderid = param($_GET, "order", 21);

$speciesQuery = new SpeciesQuery;
$speciesQuery->setOrder($orderid);

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

<div class=heading><?= $speciesQuery->getSpeciesCount() ?> species</div>

<table columns=2>		

<?
$dbQuery = $speciesQuery->performQuery();

while($info = mysql_fetch_array($dbQuery)) {
  $photoQuery = performQuery("select * from sighting where SpeciesAbbreviation='" . $info["Abbreviation"] . "' and Photo='1' order by TripDate desc");
?>

  <tr><td class=report-content>

<?
  if ($photoInfo = mysql_fetch_array($photoQuery)) {
	  echo getThumbForSightingInfo($photoInfo);
  }
?>

  <br><br>
  </td>

  <td class=report-content valign=top>
  <a href="./speciesdetail.php?id=<?= $info["objectid"] ?>"><?= $info["CommonName"] ?></a><br>
  <i><?= $info["LatinName"] ?></i><br><br>
  </td></tr>
<?
}
?>

</table>


    </div>
  </body>
</html>
