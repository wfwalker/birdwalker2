
<?php

require("./birdwalker.php");

$badAbbrevs = performQuery("select species.*,sighting.*, sighting.objectid as sightingid from sighting left join species on species.Abbreviation=sighting.SpeciesAbbreviation order by species.CommonName, sighting.SpeciesAbbreviation");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Bad Records</title>
  </head>
  <body>

<?php globalMenu(); disabledBrowseButtons(); ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Bad Records</div>
      </div>

<?php
while($sightingInfo = mysql_fetch_array($badAbbrevs)) {
	if ($sightingInfo["CommonName"] == "") {
		echo "<a href=\"./sightingedit.php?id=" . $sightingInfo["sightingid"] . "\">";
		echo $sightingInfo["SpeciesAbbreviation"];
		echo " " . $sightingInfo["LocationName"];
		echo " " . $sightingInfo["TripDate"] . "</a><br>";
	}
	else
	{
		break;
	}
}
?>
