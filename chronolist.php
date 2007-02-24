<?php

require_once("./sightingquery.php");
require_once("./birdwalker.php");


class ChronoList
{
	var $mSightingQuery;

	function ChronoList($request)
	{
		$this->mSightingQuery = new SightingQuery($request);
	}

	function draw()
	{
		performQuery("Create Temp Table",
		  "CREATE TEMPORARY TABLE tmp (
            SpeciesAbbreviation varchar(16) default NULL,
            TripDate date default NULL,
            objectid varchar(16) default NULL);");

        // here's what section 3.6.4 of the mysql manual calls:
        // "a quite inefficient trick called the MAX-CONCAT trick"
		// TODO upgrade to mysql 4.1 and use a subquery
		performQuery("Put Sightings into Temp Table",
          "INSERT INTO tmp
            SELECT SpeciesAbbreviation,
              LEFT(        MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ), 10) AS TripDate,
              0+SUBSTRING( MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ),  11) AS objectid ".
					 $this->mSightingQuery->getFromClause() . " " . 
					 $this->mSightingQuery->getWhereClause() . "  AND species.ABACountable='1' AND sighting.Exclude!='1'
            GROUP BY SpeciesAbbreviation");

		// TODO count rows in the first sightings table!

		$firstSightingQuery = performQuery("Choose first sightings",
          "SELECT " . shortNiceDateColumn("sighting.TripDate") . ",
             sighting.*, 
             species.CommonName,
             species.objectid as speciesid,
             trip.objectid as tripid, location.County, location.State
          FROM sighting, tmp, species, location, trip
          WHERE
             sighting.SpeciesAbbreviation=tmp.SpeciesAbbreviation AND
             species.Abbreviation=sighting.SpeciesAbbreviation AND
             sighting.TripDate=tmp.TripDate AND
             location.Name=sighting.LocationName AND
             trip.Date=sighting.TripDate
          ORDER BY TripDate DESC, LocationName, species.objectid;");

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
		    if ($prevSightingInfo == "" || $prevSightingInfo['TripDate'] != $sightingInfo['TripDate']) { ?>
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

		    editLink("./sightingedit.php?sightingid=" . $sightingInfo['objectid']); ?>

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
		performQuery("Create Temp Table",
		  "CREATE TEMPORARY TABLE tmp (
            SpeciesAbbreviation varchar(16) default NULL,
            TripDate date default NULL,
            objectid varchar(16) default NULL);");

        // here's what section 3.6.4 of the mysql manual calls:
        // "a quite inefficient trick called the MAX-CONCAT trick"
		// TODO upgrade to mysql 4.1 and use a subquery
		performQuery("Put Sightings into Temp Table",
          "INSERT INTO tmp
            SELECT SpeciesAbbreviation,
              LEFT(        MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ), 10) AS TripDate,
              0+SUBSTRING( MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ),  11) AS objectid ".
					 $this->mSightingQuery->getFromClause() . " " . 
					 $this->mSightingQuery->getWhereClause() . "  AND species.ABACountable='1' AND sighting.Exclude!='1'
            GROUP BY SpeciesAbbreviation");

		// TODO count rows in the first sightings table!

		$firstSightingQuery = performQuery("Choose first sightings",
          "SELECT " . shortNiceDateColumn("sighting.TripDate") . ",
             sighting.*, 
             trim(group_concat(' ', species.CommonName)) as names,
             count(species.CommonName) as counts,
              species.objectid as speciesid,
             trip.objectid as tripid, location.County, location.State
          FROM sighting, tmp, species, location, trip
          WHERE
             sighting.SpeciesAbbreviation=tmp.SpeciesAbbreviation AND
             species.Abbreviation=sighting.SpeciesAbbreviation AND
             sighting.TripDate=tmp.TripDate AND
             location.Name=sighting.LocationName AND
             trip.Date=sighting.TripDate
          group by TripDate ORDER BY TripDate DESC, LocationName, species.objectid;");

		$speciesCount = mysql_num_rows($firstSightingQuery);

?>
        <timeline>
		<events>
<?
	   while($sightingInfo = mysql_fetch_array($firstSightingQuery))
	   {
		 if ($sightingInfo['counts'] > 3)
		 {?>
		    <event startTime="<?= $sightingInfo['TripDate'] ?>" label="<?= $sightingInfo['counts'] ?> Species"/>
<?       }
		 else
		 { ?>
		    <event startTime="<?= $sightingInfo['TripDate'] ?>" label="<?= $sightingInfo['names'] ?>"/>
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
		performQuery("Create Temp Table",
		  "CREATE TEMPORARY TABLE tmp (
            SpeciesAbbreviation varchar(16) default NULL,
            TripDate date default NULL,
            objectid varchar(16) default NULL);");

		$imageWidth = 400;
		$imageHeight = 250;
		$timelineImage    = imagecreatetruecolor($imageWidth, $imageHeight);
		$black    = imagecolorallocate($timelineImage, 0, 0, 0);
		$gray    = imagecolorallocate($timelineImage, 128, 128, 128);
		$white        = imagecolorallocate($timelineImage, 255, 255, 255);
		imagefill($timelineImage, 0, 0, $white);
		imagestring($timelineImage, 2, 5, 5, "Hello World!", $black);


        // here's what section 3.6.4 of the mysql manual calls:
        // "a quite inefficient trick called the MAX-CONCAT trick"
		// TODO upgrade to mysql 4.1 and use a subquery
		performQuery("Put Sightings into Temp Table",
          "INSERT INTO tmp
            SELECT SpeciesAbbreviation,
              LEFT(        MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ), 10) AS TripDate,
              0+SUBSTRING( MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ),  11) AS objectid ".
					 $this->mSightingQuery->getFromClause() . " " . 
					 $this->mSightingQuery->getWhereClause() . "  AND species.ABACountable='1' AND sighting.Exclude!='1'
            GROUP BY SpeciesAbbreviation");

		// TODO count rows in the first sightings table!
		$minDays = performCount("first", "select min(to_days(TripDate)) from sighting");
		$maxDays = performCount("first", "select max(to_days(TripDate)) from sighting");

		$firstSightingQuery = performQuery("Choose first sightings",
          "SELECT to_days(sighting.TripDate) as tripDays
          FROM sighting, tmp, species, location, trip
          WHERE
             sighting.SpeciesAbbreviation=tmp.SpeciesAbbreviation AND
             species.Abbreviation=sighting.SpeciesAbbreviation AND
             sighting.TripDate=tmp.TripDate AND
             location.Name=sighting.LocationName AND
             trip.Date=sighting.TripDate
          ORDER BY sighting.TripDate DESC, LocationName, species.objectid;");

		$speciesCount = mysql_num_rows($firstSightingQuery);

		$index = 1;
	   while($sightingInfo = mysql_fetch_array($firstSightingQuery))
	   {
		 $x = $imageWidth * ($sightingInfo["tripDays"] - $minDays) / ($maxDays - $minDays);
		 $index = $index + 1;
		 $y = $imageHeight * $index / $speciesCount;
		 imagerectangle($timelineImage, $x - 1, $y - 1, $x + 1, $y + 1, $black);
		 imageline($timelineImage, $x, $y, $x, $imageHeight, $gray);
	   }

		performQuery("Remove temporary table", "DROP TABLE tmp;");

		imagepng($timelineImage);
		imagedestroy($timelineImage);
    }
}
?>
