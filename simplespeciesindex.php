
<?php

require("/Users/walker/Sites/birdwalker/birdwalker.php");

$lifeListCount = getSpeciesCount();
$lifeListQuery = getSpeciesQuery();

?>

<html>
  <head>
    <link title="Style" href="/~walker/birdwalker/stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | Species</title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle>Life List</div>
        <div class=pagesubtitle> <?php echo $lifeListCount ?> species</div>
      </div>
		
<?php formatSpeciesList($lifeListCount, $lifeListQuery) ?>

    </div>
  </body>
</html>
