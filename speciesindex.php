
<?php

require("./birdwalker.php");

$lifeCount = getSpeciesCount();

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Life List</title>
  </head>
  <body>

<?php navigationHeader() ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Mary and Bill&apos;s Life List</div>
        <div class=pagesubtitle><?php echo $lifeCount ?> Species</div>
      </div>

<?
$gridQueryString="select distinct(CommonName), species.objectid as speciesid, bit_or(1 << (year(TripDate) - 1995)) as mask from sighting, species where sighting.SpeciesAbbreviation=species.Abbreviation and sighting.Exclude=0 group by sighting.SpeciesAbbreviation order by speciesid";

formatSpeciesByYearTable($lifeCount, $gridQueryString);

?>

    </div>
  </body>
</html>
