
<?php

require("./birdwalker.php");

$familyid = $_GET["family"];
$orderid = floor($familyid / 100);

$whereClause = "species.Abbreviation = sighting.SpeciesAbbreviation and species.objectid >= " . $familyid * pow(10, 7) . " and species.objectid < " . ($familyid + 1) * pow(10, 7);

$familyCount = getSpeciesCount($whereClause);
$familyQuery = getSpeciesQuery($whereClause);
$familyInfo = getFamilyInfo($familyid * pow(10, 7));
$orderInfo = getOrderInfo($orderid * pow(10, 9));

$firstFamily = performCount("select floor(min(species.objectid)/pow(10,7)) from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation");
$lastFamily = performCount("select floor(max(species.objectid)/pow(10,7)) from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation");
$nextFamily = performCount("select floor(min(species.objectid)/pow(10,7)) from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation and species.objectid>" . ($familyid + 1) * pow(10, 7));
$prevFamily = performCount("select floor(max(species.objectid)/pow(10,7)) from species, sighting where sighting.SpeciesAbbreviation=species.Abbreviation and species.objectid<" . ($familyid - 1) * pow(10, 7));
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $familyInfo["LatinName"] ?></title>
  </head>

  <body>

<?php navigationHeader(); navigationButtons("./familydetail.php?family=", $familyid, $firstFamily, $prevFamily, $nextFamily, $lastFamily) ?>

    <div class=contentright>
	  <div class="titleblock">
	    <div class=pagetitle><?php echo $familyInfo["CommonName"] ?></div>
        <div class=pagesubtitle><?php echo $familyInfo["LatinName"] ?></div>
        <div class="metadata">
	      <a href="./orderdetail.php?order=<?php echo $orderid ?>">
	        Order <?php echo $orderInfo["LatinName"] ?>, <?php echo $orderInfo["CommonName"] ?>
          </a>
        </div>
      </div>

      <div class=titleblock> <?php echo $familyCount ?> species</div>

<table columns=2>		
<?php

while($info = mysql_fetch_array($familyQuery)) {
  $photoQuery = performQuery("select * from sighting where SpeciesAbbreviation='" . $info["Abbreviation"] . "' and Photo='1' order by TripDate desc");
  echo "<tr><td class=report-content>";
  if ($photoInfo = mysql_fetch_array($photoQuery)) {
	  echo getThumbForSightingInfo($photoInfo);
  }
  echo "<br><br>";
  echo "</td>";

  echo "<td class=report-content valign=top>";
  echo "<a href=\"./speciesdetail.php?id=".$info["objectid"]."\">".$info["CommonName"]."</a><br>";
  echo "<i>" . $info["LatinName"] . "</i><br><br>";
  echo "</td></tr>";
}

?>
</table>

    </div>
  </body>
</html>
