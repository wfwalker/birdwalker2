
<?php

require("./birdwalker.php");

$lifeCount = getSpeciesCount();
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
      </div>

<table cellpadding=4 columns=2>
<?
$photoQuery = performQuery("select sighting.*, species.CommonName, date_format(sighting.TripDate, '%M %e, %Y') as niceDate from sighting, species where Photo='1' and sighting.SpeciesAbbreviation=species.Abbreviation order by TripDate desc");


while($info = mysql_fetch_array($photoQuery)) {
	echo "<tr><td><a href=\"./photodetail.php?id=" . $info["objectid"] . "\"><img src=\"" . getPhotoThumbURLForSightingInfo($info) . "\" align=right></td>";
	echo "<td class=report-content valign=top>" . $info["CommonName"] . "<br/>";
	echo $info["niceDate"] . "<br/>";
	echo $info["LocationName"] .  "<br/>";
	echo $info[""] . "</td></tr>";
}

?>

    </div>
  </body>
</html>
