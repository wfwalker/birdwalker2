
<?php

require("./birdwalker.php");

$county = $_GET["county"];
$state = $_GET["state"];
$locationQuery = performQuery("select * from location where county='" . $county . "' order by State, County, Name");
$locationCount = mysql_num_rows($locationQuery);

?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $county ?> County</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
navTrailCounty($state, $county);
?>

    <div class=contentright>
      <div class="titleblock">	  
<?      rightThumbnailCounty($county); ?>
        <div class=pagetitle><?= $county ?> County</div>

      <div class=metadata>
<?        countyViewLinks($state, $county); ?>
      </div>
    </div>

<div class=heading><?= mysql_num_rows($locationQuery) ?> Locations</div>

   <? formatTwoColumnLocationList($locationQuery, false); ?>

    </div>
  </body>
</html>
