
<?php

require("./birdwalker.php");

$locationQuery = performQuery("select * from location order by State, County, Name");

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
pageThumbnail("select *, rand() as shuffle from sighting where Photo='1' order by shuffle");
?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Location Index</div>
	  <div class=pagesubtitle><?php echo mysql_num_rows($locationQuery) ?> Locations</div>
	  <div class=metadata>list | <a href="./locationindexbyyear.php">by year</a></div>
	</div>

<?php formatTwoColumnLocationList($locationQuery); ?>

    </div>
  </body>
</html>
