<?php

require_once("./birdwalker.php");
require_once("./request.php");

htmlHead("About");

$request = new Request;
$request->globalMenu();
?>

<?  topRightBanner(); ?>

    <div id="contentright">
    <table>
	<tr valign="top">
	<td width="50%" class="leftcolumn">
      <div class="subheading">Birding</div>
        <a href="http://spinnity.blogspot.com/"><img src="./images/mary.jpg" border="0" alt="Mary" align="right"></a>
        <p class="report-content">Mary took Bill to a birding class back in 1996, and we&#39;ve been birding ever since.
          We really enjoy the chance to be outside in nature together, meeting other birders and learning
          how to make those tough identifications.
          Our favorite birding guide (who led Bill on his first trip back in 1996) is Steve Shunk from
          <a href="http://www.paradisebirding.com/sys-tmpl/door/">Paradise Birding</a>.
          Steve is a very gifted birder, an excellent teacher and naturalist, and a wonderful ambassador for
          birding.</p>

	  <div class="subheading"> Field Notes and Listing</div>

		<p class="report-content">Mary and Bill maintain a joint life list, meaning that both of us have to see a particular species
        before we put it on the life list. When one of us sees a bird the other doesn&#39;t see, we mark that sighting
        "excluded" so that it doesn&#39;t get added to the life list.</p>

		<p class="report-content">In the field, we use John Shipman&#39;s <a href="http://www.nmt.edu/~shipman/z/nom/6home.html">six letter code</a>
        of bird abbreviations rather than the usual four letter bird banding codes. We find six letters much easier to remember.</p>

	  <div class="subheading">Your Sightings</div>

		<p class="report-content">If you have bird sightings that you&#39;d like to report,
		  please take a look at Cornell University&#39;s excellent <a href="http://www.ebird.com/">eBird</a> site.</p>

		<p class="report-content">If you are struggling with a particular bird identification, the
	    <a href="http://www.mbr-pwrc.usgs.gov/Infocenter/infocenter.html">Patuxent Bird Identification InfoCenter</a>
		is an excellent reference for photos, range maps, and descriptions of field marks.</p>

	  </td>
	  <td width="50%" class="rightcolumn">

	  <div class="subheading">The Photos</div>
       <a href="http://wfwalker.blogspot.com/"><img src="./images/bill.jpg" border="0" alt="Bill" align="right"></a>
 	   <p class="report-content">I use Canon <a href="http://www.fredmiranda.com/reviews/showproduct.php?product=39&sort=7&thecat=2">300mm f/4L IS</a> and
		<a href="http://consumer.usa.canon.com/ir/controller?act=ModelDetailAct&fcategoryid=154&modelid=7318">500mm f/4L</a> lenses bought at <a href="http://www.kspphoto.com/">Keeble and Shuchat</a>
        on a <a href="http://www.dpreview.com/reviews/canoneos20d/">Canon 20D</a> camera body for most of the bird photos.

		More and more these days I use the <a href="http://consumer.usa.canon.com/ir/controller?act=ModelDetailAct&fcategoryid=154&modelid=7462">Canon 1.4X II teleconverter</a>.
		I use a <a href="http://www.gitzo.com/products/metric/tripods/mountaineer/rightscreen.php3">Gitzo 1227 tripod</a> with an
		<a href="http://acratech.net/">Acratech Ultimate Ballhead</a>.
		I use the camera in <a href="http://www.luminous-landscape.com/tutorials/understanding-series/u-raw-files.shtml">Raw mode</a>, writing to <a href="http://www.sandisk.com/retail/ultra2-cf.asp">SanDisk Ultra II 2GB</a> and Lexar 1GB 40X Compact Flash cards.</p>

        <p class="report-content">I catalog my images using <a href="http://www.iview-multimedia.com/">iView MediaPro</a> on my Powerbook.
     	process them using Adobe Photoshop CS2 and its <a href="http://www.adobe.com/products/photoshop/cameraraw.html">Camera Raw</a> plug-in.
        I print using an <a href="http://www.luminous-landscape.com/reviews/printers/epson-r800.shtml">Epson R800 printer</a> onto various Epson papers.</p>

      <div class="subheading">Using the Photos</div>
        <p class="report-content">Interesting in using one of these photographs in your publication or purchasing an 8x10 print?
	      Email <a href="mailto:walker@shout.net"/>walker@shout.net</a> for more information.</p>

	  <div class="subheading">My favorite photographers</div>

		<p class="report-content">
        Big thanks for the kind words and encouragement I have gotten from
		<a href="http://www.khosla.com/forthebirds/index.html">Ashok Khosla</a>,
		<a href="http://www.cahabariverpublishing.com/about.php">Beth Maynor Young</a>,
		<a href="http://www.birdsasart.com/about.html">Arthur Morris</a>,
		<a href="http://www.birdphotography.com/">Pete LaTourette</a>,
     	<a href="http://www.kokophoto.com/">Mark Bohrer</a>,
        <a href="http://www.pbase.com/tgrey/profile">Tom Grey</a>,
        <a href="http://www.johncangphoto.com/">John Cang</a>,
        <a href="http://www.kevinsmithnaturephotos.com/">Kevin Smith</a>,
        <a href="http://www.pbase.com/robpavey/birding">Rob Pavey</a>,
        <a href="http://www.terrysteelenaturephotography.com/index.htm">Terry Steele<a/>,
        <a href="http://www.horsewings.com/whoweare.html">Kris Falco</a>,
        Cort Vaughn,
        <a href="http://www.pbase.com/birdervan">Gary and Joy Aspenall</a>,
        <a href="http://www.paradisebirding.com/sys-tmpl/pictures/">Steve Shunk</a>,
        and
        <a href="http://www.avesphoto.com/">Mike Danzenbaker</a>.</p>

    </td>
	</tr>
	</table>
<?     footer(); ?>
    </div>

<?
htmlFoot();
?>
