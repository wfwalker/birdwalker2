
<?php

require("./birdwalker.php");

$randomPhotoSightings = performQuery("select *, rand() as shuffle from sighting where Photo='1' order by shuffle");
 ?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
      <title>birdWalker | Home</title>
  </head>
  <body>

<?php
globalMenu();
$items[] = "";
navTrail($items);
disabledBrowseButtons();
?>

    <div class=contentright>
      <div class="titleblock">	  
	    <div class=pagetitle>Welcome to birdWalker</div>
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

	  <div class="report-content">
		<p>I built birdWalker as a way to collect, organize, and present
		  my birding field notes. I wanted to be able to keep track of
		  year, county, and life lists without doing very much
          record-keeping by hand.</p>

		<p>If you have bird sightings that you'd like to report,
		  please take a look at Cornell University's excellent <a href="http://www.ebird.com/">eBird</a> site.</p>

		<p>- Bill Walker</p>
	  </div>

	  <div class=heading>References</div>

	  <DIV CLASS="report-content">
		<P>
		  <A HREF="http://www.nmt.edu/~shipman/z/nom/6home.html">
			<I>A robust bird code system: the six-letter code</I>, John Shipman<BR/>
			http://www.nmt.edu/~shipman/z/nom/6home.html
		  </A>
		</P>

		<P>
		  <A HREF="http://www.aou.org/aou/birdlist.html">
			Americal Ornithological Union Checklist of North American Birds<BR/>
			http://www.aou.org/aou/birdlist.html
		  </A>
		</P>

		<P>
		  <A HREF="http://www.paradisebirding.com/sys-tmpl/door/">
			Steve Shunk, Paradise Birding<BR/>
			http://www.paradisebirding.com/sys-tmpl/door/
		  </A>
		</P>

		<p>
		  <a href="http://www.mbr-pwrc.usgs.gov/Infocenter/infocenter.html">
			Patuxent Bird Identification InfoCenter<br/>
			http://www.mbr-pwrc.usgs.gov/Infocenter/infocenter.html
		  </a>
		</p>
	  </DIV>

    </div>
  </body>
</html>
