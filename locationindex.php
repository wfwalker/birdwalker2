
<?php

require("./birdwalker.php");

$locationQuery = performQuery("SELECT * FROM location ORDER BY State, County, Name");

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Locations</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailLocations();
?>

    <div class=contentright>
      <div class="titleblock">	  
<?    rightThumbnailAll(); ?>
	  <div class=pagetitle>Locations</div>
    <div class=metadata>
      <a href="./locationindex.php">list</a> |
      <a href="./locationindexbymonth.php">by month</a> |
	  <a href="./locationindexbyyear.php">by year<a/>
    </div>
	</div>

  <div class=heading><?= mysql_num_rows($locationQuery) ?> Locations</div>

<?php formatTwoColumnLocationList($locationQuery); ?>

    </div>
  </body>
</html>
