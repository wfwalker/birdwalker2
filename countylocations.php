
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
pageThumbnailCounty($county);
?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle><?= $county ?> County</div>
        <div class=pagesubtitle><?= mysql_num_rows($locationQuery) ?> Locations</div>

      <div class=metadata>
<?        countyViewLinks($state, $county); ?>
      </div>
    </div>

   <? formatTwoColumnLocationList($locationQuery, false); ?>

    </div>
  </body>
</html>
