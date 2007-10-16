<?php

require_once("sightingquery.php");
require_once("birdwalker.php");


class ChronoList
{
	var $mSightingQuery;

	function ChronoList($request)
	{
		$this->mSightingQuery = new SightingQuery($request);
	}

	function createAndPopulateFirstSightingsTempTable()
	{
		performQuery("Create Temp Table",
		  "CREATE TEMPORARY TABLE tmp (
            species_id varchar(16) default NULL,
            trip_date date default NULL,
            id varchar(16) default NULL);");

        // here's what section 3.6.4 of the mysql manual calls:
        // "a quite inefficient trick called the MAX-CONCAT trick"
		// TODO upgrade to mysql 4.1 and use a subquery
		performQuery("Put Sightings into Temp Table",
          "INSERT INTO tmp
            SELECT species_id,
              LEFT(        MIN( CONCAT(trip.Date,lpad(sighting.id,6,'0')) ), 10) AS trip_date,
              0+SUBSTRING( MIN( CONCAT(trip.Date,lpad(sighting.id,6,'0')) ),  11) AS id ".
					 $this->mSightingQuery->getFromClause() . " " . 
					 $this->mSightingQuery->getWhereClause() . "  AND species.ABACountable='1' AND sighting.Exclude!='1'
            GROUP BY species_id");
	}

	function draw()
	{
	    $this->createAndPopulateFirstSightingsTempTable();

		// TODO count rows in the first sightings table!

		$firstSightingQuery = performQuery("Choose first sightings",
          "SELECT " . shortNiceDateColumn("trip.Date") . ",
             sighting.*, 
             species.CommonName,
             species.id as speciesid,
             trip.id as tripid, location.County, location.State
          FROM sighting, tmp, species, location, trip
          WHERE
             sighting.species_id=tmp.species_id AND
             species.id=sighting.species_id AND
             trip.Date=tmp.trip_date AND
             location.id=sighting.location_id AND
             trip.id=sighting.trip_id
          ORDER BY trip.Date DESC, location_id, species.id;");

		$speciesCount = mysql_num_rows($firstSightingQuery);

		countHeading($speciesCount, "ABA species");
?>
        <p class="report-content">
			 Note: within a single day, the order of sightings is not
			 preserved.
        </p>

		<table class="report-content" width="600px">

<?
		$prevSightingInfo = "";
		while($sightingInfo = mysql_fetch_array($firstSightingQuery))
		{ ?>
		    <tr>
		    <td nowrap>
<?
		    if ($prevSightingInfo == "" || $prevSightingInfo['trip_id'] != $sightingInfo['trip_id']) { ?>
                <a href="./tripdetail.php?tripid=<?= $sightingInfo['tripid'] ?>"><?= $sightingInfo['niceDate'] ?></a><?
		    } 
?>
	        </td>

            <td align="right">&nbsp;</td>

	        <td><a href="./speciesdetail.php?speciesid=<?= $sightingInfo['speciesid'] ?>"><?= $sightingInfo['CommonName'] ?></a>

<? 
		    if ($sightingInfo["Photo"] == "1") { ?>
		        <?= getPhotoLinkForSightingInfo($sightingInfo) ?>
<?          }

		    editLink("./sightingedit.php?sightingid=" . $sightingInfo['id']); ?>

		    </td>
		    </tr>
<?
		    if ($sightingInfo["Notes"] != "") { ?>
			    <tr><td></td><td></td><td colspan="2" class="sighting-notes"><?= $sightingInfo["Notes"] ?></td></tr><?
		    }

		    $prevSightingInfo = $sightingInfo;
		}

		performQuery("Remove temporary table", "DROP TABLE tmp;");
?>

		</table>
<?
    }

	function timelineXML()
	{
	    $this->createAndPopulateFirstSightingsTempTable();

		// TODO count rows in the first sightings table!

		$firstSightingQuery = performQuery("Choose first sightings",
          "SELECT " . shortNiceDateColumn("sighting.TripDate") . ",
             sighting.*, 
             trim(group_concat(' ', species.CommonName)) as names,
             count(species.CommonName) as counts,
              species.id as speciesid,
             trip.id as tripid, location.County, location.State
          FROM sighting, tmp, species, location, trip
          WHERE
             sighting.species_idreviation=tmp.species_id AND
             species.id=sighting.species_id AND
             sighting.trip_id=tmp.id AND
             location.id=sighting.location_id AND
             trip.id=sighting.trip_id
          group by trip.Date ORDER BY trip.Date DESC, LocationName, species.id;");

		$speciesCount = mysql_num_rows($firstSightingQuery);

?>
        <timeline>
		<events>
<?
	   while($sightingInfo = mysql_fetch_array($firstSightingQuery))
	   {
		 if ($sightingInfo['counts'] > 3)
		 {?>
		    <event startTime="<?= $sightingInfo['trip.Date'] ?>" label="<?= $sightingInfo['counts'] ?> Species"/>
<?       }
		 else
		 { ?>
		    <event startTime="<?= $sightingInfo['trip.Date'] ?>" label="<?= $sightingInfo['names'] ?>"/>
<?       }
	   }

		performQuery("Remove temporary table", "DROP TABLE tmp;");
?>

		</events>
		</timeline>
<?
    }

	function timelineImage()
	{
	    $this->createAndPopulateFirstSightingsTempTable();

		// TODO count rows in the first sightings table!
		$minDays = performCount("first", "select min(to_days(TripDate)) from sighting");
		$maxDays = performCount("first", "select max(to_days(TripDate)) from sighting");

 		$imageWidth = 400;
 		$imageHeight = 250;
 		$timelineImage    = imagecreatetruecolor($imageWidth, $imageHeight);
 		$black    = imagecolorallocate($timelineImage, 0, 0, 0);
 		$gray    = imagecolorallocate($timelineImage, 128, 128, 128);
 		$white        = imagecolorallocate($timelineImage, 255, 255, 255);
 		imagefill($timelineImage, 0, 0, $white);
 		imagestring($timelineImage, 2, 5, 5, $this->mSightingQuery->getPageTitle("Life List"), $black);

		$firstSightingQuery = performQuery("Choose first sightings",
          "SELECT to_days(sighting.TripDate) as tripDays
          FROM sighting, tmp, species, location, trip
          WHERE
             sighting.species_id=tmp.species_id AND
             species.id=sighting.species_id AND
             sighting.TripDate=tmp.TripDate AND
             location.id=sighting.location_id AND
             trip.id=sighting.trip_id
          ORDER BY sighting.trip_id, species.location_id, species.id;");

		$speciesCount = mysql_num_rows($firstSightingQuery);

		$index = $speciesCount; # start at the bottom of the graphic
        $points = array(0, $imageHeight + 10, 0, $imageHeight - 1);
		$x = 0;

	   while($sightingInfo = mysql_fetch_array($firstSightingQuery))
	   {
		 $x = $imageWidth * ($sightingInfo["tripDays"] - $minDays) / ($maxDays - $minDays);
		 $index = $index - 1; # increment upward for each sighting
		 $y = $imageHeight * $index / $speciesCount;
		 array_push($points, $x);
		 array_push($points, $y);
	   }
	   
	   array_push($points, $imageWidth, $y);
	   array_push($points, $imageWidth, $imageHeight + 10);

	   imagefilledpolygon($timelineImage, $points, count($points) / 2, $gray);
		performQuery("Remove temporary table", "DROP TABLE tmp;");

		imagepng($timelineImage);
		imagedestroy($timelineImage);
    }
}
?>
