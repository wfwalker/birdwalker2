
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
pageThumbnail("SELECT *, rand() AS shuffle FROM sighting WHERE Photo='1' ORDER BY shuffle");
?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Locations</div>
	  <div class=pagesubtitle><?= mysql_num_rows($locationQuery) ?> Locations</div>
	  <div class=metadata>list | <a href="./locationindexbyyear.php">by year</a></div>
	</div>

<?php formatTwoColumnLocationList($locationQuery); ?>

    </div>
  </body>
</html>
