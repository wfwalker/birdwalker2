
<?php

require("./birdwalker.php");

$speciesQuery = getSpeciesQuery();

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Speciess</title>
  </head>
  <body>

<?php globalMenu(); disabledBrowseButtons(); navTrailBirds("list | <a href=\"./speciesindexbyyear.php\">by year</a>"); ?>

    <div class=contentright>
      <div class="titleblock">	  
	  <div class=pagetitle>Species Index</div>
	  <div class=pagesubtitle><?php echo mysql_num_rows($speciesQuery) ?> Species</div>
      </div>

<?php formatTwoColumnSpeciesList($speciesQuery); ?>

    </div>
  </body>
</html>
