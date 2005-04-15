<?php

require_once("./sightingquery.php");
require_once("./birdwalker.php");


class ChronoList
{
	var $mSightingQuery;

	function ChronoList()
	{
		$this->mSightingQuery = new SightingQuery;
	}

	function setFromRequest($get)
	{
		$this->mSightingQuery->setFromRequest($get);
	}

	function draw()
	{
		performQuery("CREATE TEMPORARY TABLE tmp (
            SpeciesAbbreviation varchar(16) default NULL,
            TripDate date default NULL,
            objectid varchar(16) default NULL);");

        // here's what section 3.6.4 of the mysql manual calls:
        // "a quite inefficient trick called the MAX-CONCAT trick"
		// TODO upgrade to mysql 4.1 and use a subquery
		performQuery("
          INSERT INTO tmp
            SELECT SpeciesAbbreviation,
              LEFT(        MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ), 10) AS TripDate,
              0+SUBSTRING( MIN( CONCAT(TripDate,lpad(sighting.objectid,6,'0')) ),  11) AS objectid ".
					 $this->mSightingQuery->getFromClause() . " " . 
					 $this->mSightingQuery->getWhereClause() . "  AND species.ABACountable='1' AND sighting.Exclude!='1'
            GROUP BY SpeciesAbbreviation");

		// TODO count rows in the first sightings table!

		$firstSightingQuery = performQuery("
          SELECT
             date_format(sighting.TripDate, '%M %e, %Y') as niceDate,
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
        <p class=report-content>
			 Note: within a single day, the order of sightings is not
			 preserved.
        </p>

		<table class=report-content width="600px">

<?
		while($sightingInfo = mysql_fetch_array($firstSightingQuery))
		{ ?>
		    <tr>
		    <td nowrap>
<?
		    if ($prevSightingInfo['TripDate'] != $sightingInfo['TripDate']) { ?>
                <a href="./tripdetail.php?tripid=<?= $sightingInfo['tripid'] ?>"><?= $sightingInfo['niceDate'] ?></a><?
		    } 
?>
	        </td>

            <td align=right>&nbsp;</td>

	        <td><a href="./speciesdetail.php?speciesid=<?= $sightingInfo['speciesid'] ?>"><?= $sightingInfo['CommonName'] ?></a>

<? 
		    if ($sightingInfo["Photo"] == "1") { ?>
		        <?= getPhotoLinkForSightingInfo($sightingInfo) ?>
<?          }

		    editLink("./sightingedit.php?id=" . $sightingInfo['objectid']); ?>

		    </td>
		    </tr>
<?
		    if ($sightingInfo["Notes"] != "") { ?>
			    <tr><td></td><td></td><td colspan=2 class=sighting-notes><?= $sightingInfo["Notes"] ?></td></tr><?
		    }

		    $prevSightingInfo = $sightingInfo;
		}

		performQuery("DROP TABLE tmp;");
?>

		</table>
<?
    }
}
?>