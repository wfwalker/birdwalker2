<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"?>

<?
require("./birdwalker.php");
$latestTrips = performQuery("select *, date_format(Date, '%M %e, %Y') AS niceDate from trip order by Date desc limit 5");
?>

<rss version="2.0">
  <channel>
    <title>birdWalker</title>
    <link>http://sven.spflrc.org/~walker/index.php</link>
    <description>Mary and Bill&apos;s birding field notes and photos</description>

    <language>en-US</language>
    <generator>birdWalker</generator>

<?    for ($index = 0; $index < 5; $index++)
	  {
		  $info = mysql_fetch_array($latestTrips); ?>

      <item>
          <pubDate><?= $info["niceDate"] ?></pubDate>
          <title><?= $info["Name"] ?></title>
		   <category>trip</category>
          <link>http://sven.spflrc.org/~walker/tripdetail.php?id=<?=$info["objectid"]?></link>
          <description><?= htmlentities($info["Notes"]) ?></description>
      </item>
<?	  } ?>

    </channel>
</rss>