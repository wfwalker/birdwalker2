
<?php

require("./birdwalker.php");

$siteInfo = getLocationInfo($_GET['id']);

$whereClause = "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName='" . $siteInfo["Name"]. "'";
$siteListQuery = getSpeciesQuery($whereClause);
$siteListCount = getSpeciesCount($whereClause);

$tripQuery = getTripQuery("sighting.LocationName='" . $siteInfo["Name"]. "' and sighting.TripDate=trip.Date");
$tripCount = getTripCount("sighting.LocationName='" . $siteInfo["Name"]. "' and sighting.TripDate=trip.Date");
?>

<html>

<head>
<link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $siteInfo["Name"] ?></title>
</head>

<body>

<?php navigationHeader() ?>

<div class="contentright">
      <div class="titleblock">
	  <div class=pagetitle><?php echo $siteInfo["Name"] ?></div>
<div class=pagesubtitle><?php echo $siteInfo["County"] ?> County</div>
<div class=metadata><?php echo $siteInfo["City"] ?>, <?php echo $siteInfo["State"] ?></div>
<div class=metadata><?php echo $siteListCount . " Species seen on " . $tripCount . " trips" ?></div>

<?php
if (strlen($siteInfo["ReferenceURL"]) > 0) {
	echo "<div><a href=\"" . $siteInfo["ReferenceURL"] . "\">Location Website</a></div>";
}
?>

</div>

<p class=sighting-notes><?php echo $siteInfo["Notes"] ?></p>

  <?php
  if ($tripCount < 5)
  {
	  echo "
	  <div class=\"titleblock\">Seen ". $siteListCount . " species at this location</div>";
	  formatSpeciesList($siteListCount, $siteListQuery);
	  echo "<div class=\"titleblock\">Visited on " . $tripCount . " trips</div>";

	  // list the trips that included this location
	  while($tripInfo = mysql_fetch_array($tripQuery)) {
		  echo "<div class=firstcell><a href=\"./tripdetail.php?id=" . $tripInfo["objectid"] . "\">" . $tripInfo["Name"] . " (" . $tripInfo["Date"] .  ")</a></div>";
	  }
  }
  else
  {
	  $gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species where sighting.LocationName='" . $siteInfo["Name"] . "' and sighting.SpeciesAbbreviation=species.Abbreviation group by sighting.SpeciesAbbreviation order by speciesid";

	  formatSpeciesByYearTable($siteListCount, $gridQueryString);
   }

?>

</div>
</body>
</html>
