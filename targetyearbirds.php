
<?php

require("./birdwalker.php");


$lastSightingQuery = performQuery("select species.CommonName, species.objectid as speciesid, sighting.objectid, max(sighting.TripDate) as lastDate from sighting, species where species.Abbreviation=sighting.SpeciesAbbreviation and Exclude='0' group by SpeciesAbbreviation order by species.objectid;");

?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | Target Year Birds</title>
</head>

<body>

<?php globalMenu(); disabledBrowseButtons(); navTrailBirds(); ?>

<div class="contentright">

<div class="titleblock">
    <div class="pagetitle">Target birds for 2004</div>
</div>

<?php
	  while($info = mysql_fetch_array($lastSightingQuery))
	  {
		  if ($info["lastDate"] < '2004-01-01')
		  {
			  echo "<div class=firstcell><a href=\"./speciesdetail.php?id=".$info["speciesid"]."\">" . $info["CommonName"] . "</a> last seen " . $info["lastDate"] . "</div>";
		  }
	  }

?>

</div>
</body>
</html>


