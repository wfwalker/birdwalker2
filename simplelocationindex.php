
<?php

require("/Users/walker/Sites/birdwalker/birdwalker.php");

$locationListCount = getLocationCount();
$locationListQuery = getLocationQuery();

?>

<html>
  <head>
    <link title="Style" href="/~walker/birdwalker/stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | Locations</title>
  </head>
  <body>

<?php navigationHeader() ?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle>Location Reports</div>
        <div class=pagesubtitle><?php echo $locationListCount ?> locations</div>
      </div>

<?php formatLocationList($locationListCount, $locationListQuery) ?>

    </div>
  </body>
</html>
