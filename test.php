
<?php

   require("./birdwalker.php");

   $randomPhotoSightings = performQuery("select *, rand() as shuffle from sighting where Photo='1' order by shuffle");
   ?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
      <title>birdWalker | Test</title>
  </head>
  <body>

	<?php globalMenu() ?>

    <div class=contentright>
      <div class="titleblock">	  
	    <div class=pagetitle>birdWalker test</div>
      </div>

      <table width="100%"><tr>
<?php 
for ($index = 0; $index < 5; $index++)
{
	$info = mysql_fetch_array($randomPhotoSightings);
	echo "<td>" . getThumbForSightingInfo($info) . "</td>";
}
?>
			 </tr></table>

		  <p>&nbsp;</p>

		  <div class="col1">
			<div class="report-content"><a href="./chronolifelist.php">chronolifelist</a></div>
			<div class="report-content"><a href="./countydetail.php?county=San%20Mateo">countydetail</a></div>
			<div class="report-content"><a href="./countyindex.php">countyindex</a></div>
			<div class="report-content"><a href="./errorcheck.php">errorcheck</a></div>
			<div class="report-content"><a href="./familydetail.php?family=1001">familydetail</a></div>
			<div class="report-content"><a href="./locationcreate.php">locationcreate</a></div>
			<div class="report-content"><a href="./locationdetail.php?id=132">locationdetail</a></div>
			<div class="report-content"><a href="./locationindex.php">locationindex</a></div>
			<div class="report-content"><a href="./locationindexbyyear.php">locationindexbyyear</a></div>
			<div class="report-content"><a href="./onthisdate.php">onthisdate</a></div>
			<div class="report-content"><a href="./orderdetail.php?order=10">orderdetail</a></div>
			<div class="report-content"><a href="./photodetail.php?id=8859">photodetail</a></div>
			<div class="report-content"><a href="./photoindex.php">photoindex</a></div>
			<div class="report-content"><a href="./photoindextaxo.php">photoindextaxo</a></div>
			<div class="report-content"><a href="./photosneeded.php">photosneeded</a></div>
		  </div>
		  <div class="col2">
			<div class="report-content"><a href="./sightingdetail.php?id=8905">sightingdetail</a></div>
			<div class="report-content"><a href="./sightingedit.php">sightingedit</a></div>
			<div class="report-content"><a href="./sightinglist.php?locationid=68&year=2004&speciesid=5010070100">sightinglist</a></div>
			<div class="report-content"><a href="./speciesdetail.php?id=10010040100">speciesdetail</a></div>
			<div class="report-content"><a href="./speciesindex.php">speciesindex</a></div>
			<div class="report-content"><a href="./speciesindex2.php">speciesindex2</a></div>
			<div class="report-content"><a href="./specieslist.php?locationid=114&year=2003">specieslist</a></div>
			<div class="report-content"><a href="./speciesphotos.php?id=5010070100">speciesphotos</a></div>
			<div class="report-content"><a href="./statedetail.php?state=OR">statedetail</a></div>
			<div class="report-content"><a href="./stateindex.php">stateindex</a></div>
			<div class="report-content"><a href="./targetyearbirds.php">targetyearbirds</a></div>
			<div class="report-content"><a href="./tripcreate.php">tripcreate</a></div>
			<div class="report-content"><a href="./tripdetail.php?id=295">tripdetail</a></div>
			<div class="report-content"><a href="./tripedit.php">tripedit</a></div>
			<div class="report-content"><a href="./tripindex.php">tripindex</a></div>
			<div class="report-content"><a href="./yeardetail.php?year=2002">yeardetail</a></div>
		  </div>
		  </div>
		  </body>
		  </html>
