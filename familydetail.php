
<?php

require("./birdwalker.php");

$familyid = $_GET["family"];
$orderid = floor($familyid / 100);

$whereClause = "species.Abbreviation = sighting.SpeciesAbbreviation and species.objectid >= " . $familyid * pow(10, 7) . " and species.objectid < " . ($familyid + 1) * pow(10, 7);

$familyQuery = getSpeciesQuery($whereClause);
$familyCount = mysql_num_rows($familyQuery);
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

<?php
globalMenu();
browseButtons("./familydetail.php?family=", $familyid, $firstFamily, $prevFamily, $nextFamily, $lastFamily);
$items[] = "<a href=\"./orderdetail.php?order=" . $orderInfo["objectid"] / pow(10, 9) . "\">" . strtolower($orderInfo["LatinName"]) . "</a>";
$items[] = strtolower($familyInfo["LatinName"]);
navTrailBirds($items);
 ?>

    <div class=contentright>
	  <div class="titleblock">
	    <div class=pagetitle><?php echo $familyInfo["CommonName"] ?></div>
        <div class=pagesubtitle><?php echo $familyInfo["LatinName"] ?></div>
        <div class=metadata> <?php echo $familyCount ?> species</div>
      </div>


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
