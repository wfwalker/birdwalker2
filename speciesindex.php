
<?php

require("./birdwalker.php");

$speciesQuery = getSpeciesQuery();

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Species</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailBirds();
?>

    <div class=contentright>
      <div class="titleblock">	  
<?    rightThumbnailAll(); ?>
	  <div class=pagetitle>Species</div>
	  <div class=metadata>list | <a href="./speciesindexbymonth.php">by month</a> | <a href="./speciesindexbyyear.php">by year</a></div>
      </div>

    <div class=heading><?= mysql_num_rows($speciesQuery) ?> Species</div>

<?php formatTwoColumnSpeciesList($speciesQuery); ?>

    </div>
  </body>
</html>
