
<?php

require("./birdwalker.php");

$latestTrips = performQuery("select *, date_format(Date, '%M %e, %Y') AS niceDate from trip order by Date desc limit 5");
 ?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
      <title>birdWalker | Home</title>
  </head>
  <body>

<?php
globalMenu();
disabledBrowseButtons();
?>

    <div class=contentright>

	  <p>Welcome to <code>birdWalker</code>! This website contains Bill and Mary&apos;s birding field notes, including
	  trip, county, state, and year lists. Our latest trips are listed below, other indices
	  are available from the links on the left.</p>


<?    for ($index = 0; $index < 5; $index++)
	  {
		  $info = mysql_fetch_array($latestTrips);
          $tripSpeciesCount = performCount("SELECT COUNT(DISTINCT(sighting.objectid)) from sighting where sighting.TripDate='" . $info["Date"] . "'"); ?>

		  <p>&nbsp;</p>

          <div class="pagesubtitle"><?= $info["niceDate"] ?></div>

		  <div class="titleblock">
<?         rightThumbnail("SELECT * FROM sighting WHERE Photo='1' AND TripDate='" . $info["Date"] . "' LIMIT 1"); ?>
              <span class="pagetitle"><a href="./tripdetail.php?id=<?=$info["objectid"]?>"/><?= $info["Name"] ?></a></span>
              <div class="subheading"><?= $tripSpeciesCount ?> species</div>
          </div>


          <div class=report-content><?= $info["Notes"] ?><br clear="all"/></div>
<?	  } ?>

  </body>
</html>
