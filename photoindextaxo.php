
<?php

require("./birdwalker.php");

$photoSpecies = performQuery("select distinct species.*, count(distinct sighting.objectid) as photoCount from species, sighting where species.Abbreviation=sighting.SpeciesAbbreviation and sighting.Photo='1' group by sighting.SpeciesAbbreviation order by species.objectid");
$photoCount = performCount("select count(*) from sighting where Photo='1'");

$randomPhotoSightings = performQuery("select *, rand() as shuffle from sighting where Photo='1' order by shuffle");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Photo List</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailPhotos("by species | <a href=\"./photoindex.php\">by date</a>");
?>


<div class=thumb><?php  if (mysql_num_rows($randomPhotoSightings) > 0) { $photoInfo = mysql_fetch_array($randomPhotoSightings); if (mysql_num_rows($randomPhotoSightings) > 0) echo "<td>" . getThumbForSightingInfo($photoInfo) . "</td>"; } ?></div>

    <div class=contentright>
      <div class="titleblock">	  
	    <div class=pagetitle>Photo Index</div>
<div class=pagesubtitle><?php echo $photoCount . " photos covering " . mysql_num_rows($photoSpecies) . " species"; ?></div>
      </div>

<table cellpadding=4 columns=2>
<?

while($info = mysql_fetch_array($photoSpecies)) {
		  $orderNum =  floor($info["objectid"] / pow(10, 9));
		
		  if (getBestTaxonomyID($prevInfo["objectid"]) != getBestTaxonomyID($info["objectid"]))
		  {
			  $taxoInfo = getBestTaxonomyInfo($info["objectid"]);
			  echo "<div class=\"heading\">" . $taxoInfo["CommonName"] . "</div>";
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
