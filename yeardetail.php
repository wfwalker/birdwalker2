
<?

require("./birdwalker.php");

$theYear = $_GET["year"];

$yearCount = performCount("
    SELECT COUNT(DISTINCT species.objectid) FROM species, sighting
      WHERE sighting.Exclude!='1' AND species.Abbreviation=sighting.SpeciesAbbreviation
      AND year(sighting.TripDate)='" . $theYear . "'"); ?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $theYear ?> Report</title>
  </head>
  <body>

<?
globalMenu();
browseButtons("./yeardetail.php?year=", $theYear, 1996, $theYear - 1, $theYear + 1, 2004);
navTrailBirds();
?>

    <div class=contentright>
      <div class="titleblock">	  
<?      rightThumbnail("
            SELECT sighting.*, rand() AS shuffle
                FROM sighting
                WHERE sighting.Photo='1' AND Year(TripDate)='" . $theYear . "'
                ORDER BY shuffle LIMIT 1"); ?>
        <div class=pagetitle><?= $theYear ?> List</div>
    </div>


    <div class=heading><?= $yearCount ?> species</div>

<? $monthlyTotal = performQuery("
    SELECT COUNT(DISTINCT sighting.SpeciesAbbreviation) AS count, month(sighting.TripDate) AS month
      FROM sighting, species
      WHERE sighting.SpeciesAbbreviation=species.Abbreviation
      AND Year(sighting.TripDate)=" . $theYear . "
      GROUP BY month");

   formatSpeciesByMonthTable("
     WHERE sighting.Exclude='0' AND sighting.SpeciesAbbreviation=species.Abbreviation AND sighting.LocationName=location.Name
       AND year(sighting.TripDate)='" . $theYear . "'",
      "&year=" . $theYear, $monthlyTotal); ?>

    </div>
  </body>
</html>
