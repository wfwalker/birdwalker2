
<?php

require("./birdwalker.php");

$stateName = $_GET["state"];
$whereClause =  "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.State='" . $stateName . "'";
$stateListCount = getFancySpeciesCount($whereClause);
$stateListQuery = getFancySpeciesQuery($whereClause);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $stateName ?> State List</title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle><?php echo $stateName ?> State List</div>
        <div class=pagesubtitle> <?php echo $stateListCount ?> species</div>
      </div>
		
<?php

	$divideByTaxo = ($stateListCount > 30);
	
	while($info = mysql_fetch_array($stateListQuery))
	{
		$orderNum =  floor($info["objectid"] / pow(10, 9));
		
		if ($divideByTaxo && (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"])))
		{
			$taxoInfo = getBestTaxonomyInfo($info["objectid"]);
			echo "<div class=\"titleblock\">" . $taxoInfo["CommonName"] . "</div>";
		}

		echo "<div class=firstcell><a href=\"./speciesdetail.php?id=".$info["objectid"]."\">".$info["CommonName"]."</a></div>";

		if (strlen($info["Notes"]) > 0) {
			echo "<div class=sighting-notes>" . $info["Notes"] . "</div>";
		}
		
		$prevInfo = $info;
	}


?>

    </div>
  </body>
</html>
