
<?

require("./birdwalker.php");
require("./speciesquery.php");

$theYear = param($_GET, "year", "1998");
$speciesQuery = new SpeciesQuery;
$speciesQuery->setYear($theYear);

?>

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
                ORDER BY shuffle LIMIT 1", true); ?>
        <div class=pagetitle><?= $theYear ?> List</div>
    </div>


		<div class=heading><?= $speciesQuery->getSpeciesCount() ?> species</div>

<? $speciesQuery->formatSpeciesByMonthTable(); ?>

    </div>
  </body>
</html>
