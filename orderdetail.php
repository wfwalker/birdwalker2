
<?php

require("/Users/walker/Sites/birdwalker/birdwalker.php");

$orderid = $_GET["order"];

$whereClause = "species.Abbreviation = sighting.SpeciesAbbreviation and species.objectid >= " . $orderid * pow(10, 9) . " and species.objectid < " . ($orderid + 1) * pow(10, 9);

$orderCount = getSpeciesCount($whereClause);
$orderQuery = getSpeciesQuery($whereClause);
$orderInfo = getOrderInfo($orderid * pow(10, 9));

?>

<html>
  <head>
    <link title="Style" href="/~walker/birdwalker/stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $orderInfo["LatinName"] ?></title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle><?php echo $orderInfo["CommonName"] ?></div>
        <div class=pagesubtitle> <?php echo $orderInfo["LatinName"] ?></div>
      </div>

      <div class=titleblock> <?php echo $orderCount ?> species</div>
		
<?php

$divideByFamilys = ($orderCount > 20);
$prevFamilyNum = -1;

while($info = mysql_fetch_array($orderQuery))
{
	$familyNum =  floor($info["objectid"] / pow(10, 7));
	
	if ($divideByFamilys && ($prevFamilyNum != $familyNum))
    {
		$familyInfo = getFamilyInfo($familyNum * pow(10, 7));
		echo "<div class=\"titleblock\"><i>" . $familyInfo["LatinName"] . "</i>, " . $familyInfo["CommonName"] . "</div>";
    }
	
	
	echo "<div class=firstcell><a href=/~walker/birdwalker/speciesdetail.php?id=".$info["objectid"].">".$info["CommonName"] . "</a></div>";
	$prevFamilyNum = $familyNum;
}

?>

    </div>
  </body>
</html>
