
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
pageThumbnail("select *, rand() as shuffle from sighting where Photo='1' order by shuffle");
?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Species</div>
	  <div class=pagesubtitle><?= mysql_num_rows($speciesQuery) ?> Species</div>
	  <div class=metadata>list | <a href="./speciesindexbyyear.php">by year</a></div>
      </div>

<?php formatTwoColumnSpeciesList($speciesQuery); ?>

    </div>
  </body>
</html>
