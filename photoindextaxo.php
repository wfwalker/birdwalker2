
<?php

require("./birdwalker.php");

$photoSpecies = performQuery("select distinct species.*, count(distinct sighting.objectid) as photoCount from species, sighting where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.Photo='1' group by sighting.SpeciesAbbreviation order by species.objectid");

$photoCount = performCount("select count(*) from sighting where Photo='1'");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Photo List</title>
  </head>
  <body>

<?php navigationHeader() ?>

    <div class=contentright>
      <div class="titleblock">	  
	    <div class=pagetitle>Photo Index</div>
<div class=pagesubtitle><?php echo $photoCount . " photos, " . mysql_num_rows($photoSpecies) . " species photographed"; ?></div>
      </div>

<table cellpadding=4 columns=2>
<?

while($info = mysql_fetch_array($photoSpecies)) {
		  $orderNum =  floor($info["objectid"] / pow(10, 9));
		
		  if (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"]))
		  {
			  $taxoInfo = getBestTaxonomyInfo($info["objectid"]);
			  echo "<div class=\"titleblock\">" . $taxoInfo["CommonName"] . "</div>";
		  }

		  if ($info["photoCount"] > 1)
		  {
			  echo "<div class=firstcell><a href=\"./speciesphotos.php?id=".$info["objectid"]."\">".$info["CommonName"]."</a> (" . $info["photoCount"] . ")</div>";
		  }
		  else
		  {
			  echo "<div class=firstcell><a href=\"./speciesphotos.php?id=".$info["objectid"]."\">".$info["CommonName"]."</a></div>";
		  }
		
		  $prevInfo = $info;
}

?>

    </div>
  </body>
</html>
