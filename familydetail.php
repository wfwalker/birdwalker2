
<?php

require("./birdwalker.php");

$familyid = $_GET["family"];
$orderid = floor($familyid / 100);

$whereClause = "species.Abbreviation = sighting.SpeciesAbbreviation and species.objectid >= " . $familyid * pow(10, 7) . " and species.objectid < " . ($familyid + 1) * pow(10, 7);

$familyCount = getSpeciesCount($whereClause);
$familyQuery = getSpeciesQuery($whereClause);
$familyInfo = getFamilyInfo($familyid * pow(10, 7));
$orderInfo = getOrderInfo($orderid * pow(10, 9));

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $familyInfo["LatinName"] ?></title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class=contentright>
	  <div class="titleblock">
	    <div class=pagetitle><?php echo $familyInfo["CommonName"] ?></div>
        <div class=pagesubtitle><?php echo $familyInfo["LatinName"] ?></div>
        <div class="metadata">
	      Order:
	      <a href="./orderdetail.php?order=<?php echo $orderid ?>">
	        <?php echo $orderInfo["LatinName"] ?>, <?php echo $orderInfo["CommonName"] ?>
          </a>
        </div>
      </div>

      <div class=titleblock> <?php echo $familyCount ?> species</div>
		
<?php

while($info = mysql_fetch_array($familyQuery)) {
  echo "<div class=firstcell><a href=\"./speciesdetail.php?id=".$info["objectid"]."\">".$info["CommonName"]."</a></div>";
  $prevInfo = $info;
}

?>

    </div>
  </body>
</html>
