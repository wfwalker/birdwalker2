
<?php

require("./birdwalker.php");

$theYear = $_GET["year"];

$yearCount = performCount("select count(distinct species.objectid) from species, sighting where sighting.Exclude!='1' and species.Abbreviation=sighting.SpeciesAbbreviation and year(sighting.TripDate)='" . $theYear . "'");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $theYear ?> Report</title>
  </head>
  <body>

<?php
globalMenu();
browseButtons("./yeardetail.php?year=", $theYear, 1996, $theYear - 1, $theYear + 1, 2004);
navTrailBirds();
pageThumbnail("
    SELECT sighting.*, rand() AS shuffle
      FROM sighting
      WHERE sighting.Photo='1' AND Year(TripDate)='" . $theYear . "'
      ORDER BY shuffle LIMIT 1");
?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle> <?= $theYear ?> List</div>
        <div class=pagesubtitle><?= $yearCount ?> species</div>
      </div>

<?

$gridQueryString = "
    SELECT DISTINCT(CommonName), species.objectid AS speciesid, BIT_OR(1 << Month(TripDate)) AS mask
      FROM sighting, species
      WHERE sighting.Exclude='0' AND sighting.SpeciesAbbreviation=species.Abbreviation
      AND year(sighting.TripDate)='" . $theYear . "'
      GROUP BY sighting.SpeciesAbbreviation ORDER BY speciesid";

$monthlyTotal = performQuery("
    SELECT COUNT(DISTINCT sighting.SpeciesAbbreviation) AS count, month(sighting.TripDate) AS month
      FROM sighting, species
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND Year(sighting.TripDate)=" . $theYear . "
      GROUP BY month");

formatSpeciesByMonthTable($gridQueryString, "&year=" . $theYear, $monthlyTotal); ?>

</table>

    </div>
  </body>
</html>
