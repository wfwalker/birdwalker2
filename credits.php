
<?php

require_once("./birdwalker.php");

$randomPhotoSightings = performQuery("SELECT *, rand() AS shuffle FROM sighting WHERE Photo='1' ORDER BY shuffle LIMIT 5");
 ?>

<html>

  <? htmlHead("About"); ?>

  <body>

<?php
globalMenu();
$items[] = "";
navTrail($items);
disabledBrowseButtons();
?>

    <div class=contentright>

	  <p>Welcome to <code>birdWalker</code>! This website contains Bill and Mary&#39;s birding field notes, including
	  trip, county, state, and year lists. Our latest trips are listed below, other indices
	  are available from the links on the left.</p>

      <table width="100%"><tr>

<?    for ($index = 0; $index < 5; $index++)
	  {
		  $info = mysql_fetch_array($randomPhotoSightings); ?>
		  <td><?= getThumbForSightingInfo($info) ?></td>
<?	  } ?>

      </tr></table>

	  <p>&nbsp;</p>

      <div class="heading">About Birding</div>
        <p>Mary took Bill to a birding class back in 1996, and we&#39;ve been birding ever since.
          We really enjoy the chance to be outside in nature together, meeting other birders and learning
          how to make those tough identifications.
          Our favorite birding guide (who led Bill on his first trip back in 1996) is Steve Shunk from
          <a href="http://www.paradisebirding.com/sys-tmpl/door/">Paradise Birding</a>.
          Steve is a very gifted birder, an excellent teacher and naturalist, and a wonderful ambassador for
          birding.</p>

	  <div class="heading">About the Photos</div>
 	  <p>I use a <a href="http://www.fredmiranda.com/reviews/showproduct.php?product=39&sort=7&thecat=2">Canon 300mm f/4L IS</a> lens
        on a <a href="http://www.dpreview.com/reviews/canoneos20d/">Canon 20D</a> camera body for most of the bird photos.
        Sometimes I rent a 500mm lens from my local camera store, <a href="http://www.kspphoto.com/"> Keeble and Shuchat.</a>
		More and more these days I use the Canon 1.4X II teleconverter.
		I use  a Gitzo 1227 tripod with an
		<a href="http://acratech.net/">Acratech Ultimate Ballhead</a>.
		I use the camera in RAW mode, writing to Lexar 1GB 40X Compact Flash cards.</p>

        <p>I catalog my images using <a href="http://www.iview-multimedia.com/">iView MediaPro</a> on my Powerbook.
     	process them using Adobe Photoshop CS and its Camera Raw plug-in.
	    I sometimes apply the <a href="http://www.picturecode.com/">PictureCode Noise Ninja</a> Photoshop plug-in.
        I print using an Epson R800 printer onto various Epson papers.</p>

	  <div class="heading">About Field Notes and Listing</div>

		<p>Mary and Bill maintain a joint life list, meaning that both of us have to see a particular species
        before we put it on the life list. When one of us sees a bird the other doesn&#39;t see, we mark that sighting
        "excluded" so that it doesn&#39;t get added to the life list.</p>

		<p>In the field, we use John Shipman&#39;s <a href="http://www.nmt.edu/~shipman/z/nom/6home.html">six letter code</a>
        of bird abbreviations rather than the usual four letter bird banding codes. We find six letters much easier to remember.</p>

	  <div class="heading">About your Sightings</div>

		<p>If you have bird sightings that you&#39;d like to report,
		  please take a look at Cornell University&#39;s excellent <a href="http://www.ebird.com/">eBird</a> site.</p>

		<p>If you are struggling with a particular bird identification, the
	    <a href="http://www.mbr-pwrc.usgs.gov/Infocenter/infocenter.html">Patuxent Bird Identification InfoCenter</a>
		is an excellent reference for photos, range maps, and descriptions of field marks.</p>

	  <div class="heading">My favorite photographers</div>
        Big thanks to the kind words and encouragement I have gotten from
		<a href="http://www.birdphotography.com/">Pete LaTourette</a>,
     	<a href="http://www.kokophoto.com/">Mark Bohrer</a>,
        <a href="http://www.pbase.com/tgrey/profile">Tom Grey</a>,
        <a href="http://www.johncangphoto.com/">John Cang</a>,
        <a href="http://www.kevinsmithnaturephotos.com/">Kevin Smith</a>,
        <a href="http://www.pbase.com/robpavey/birding">Rob Pavey</a>,
        Terry Steele,
        Kris Falco,
        Cort Vaughn,
        <a href="http://www.pbase.com/birdervan">Gary and Joy Aspenall</a>,
        Steve Shunk,
        and
        <a href="http://www.avesphoto.com/">Mike Danzenbaker</a>
    </div>
  </body>
</html>
