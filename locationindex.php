
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

<?php globalMenu(); disabledBrowseButtons(); navTrailLocations("&gt; list | <a href=\"./locationindexbyyear.php\">by year</a>"); ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Location Index</div>
	  <div class=pagesubtitle><?php echo mysql_num_rows($locationQuery) ?> Locations</div>
      </div>

<?php formatTwoColumnLocationList($locationQuery); ?>

    </div>
  </body>
</html>
