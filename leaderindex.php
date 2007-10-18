<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;

htmlHead("Counties");
$request->globalMenu();

?>


    <div id="topright-trip">
      <div class="pagekind">Index</div>
      <div class="pagetitle">Leaders</div>
	</div>

    <div id="contentright">

<table class="metadata" cellpadding="0" cellspacing="0" width="100%">

<?php

$yearArray = null;
$lastStateAccumulated = "NONE";
$prevState = "NONE";
$countyToAccumulate = "NONE";
$countyStats = performQuery("Find species counts per leader", "
    SELECT trips.Leader, COUNT(DISTINCT sightings.SpeciesAbbreviation) AS
      SpeciesCount, year(trips.date) AS theyear
      FROM trip, sighting
      WHERE trips.date=trips.Date
      GROUP BY trips.Leader, theyear
      ORDER BY trips.Leader, theyear"); ?>

    <tr><td></td><? insertYearLabels(); ?></tr>

<?
while ($info = mysql_fetch_array($countyStats))
{
	$leader = trim($info["Leader"]);
	$theYear = $info["theyear"];
	$speciesCount = $info["SpeciesCount"];
		
	if (($yearArray != null) && ($leaderToAccumulate != $leader))
	{ ?>

		<tr><td class="firstcell">
			<a href="./leaderdetail.php?leader=<?= urlencode($leaderToAccumulate) ?>">
			<?= $leaderToAccumulate ?>
			</a>
			</td>

<?	for ($year = getEarliestYear(); $year <= getLatestYear(); $year++)
	{ ?>
        <td class="bordered" align="right">
            <a href="./specieslist.php?leader=<?= urlEncode($leaderToAccumulate) ?>&year=<?= $year ?>">
	          &nbsp; <? if (array_key_exists($year, $yearArray)) echo $yearArray[$year]; ?>
            </a>
        </td>
<?	}?>

		</tr>

<?		$yearArray = null;
	}

	$leaderToAccumulate = $leader;
	$yearArray[$theYear] = $speciesCount;
}

 ?>

</table>

<?
footer();
?>

    </div>

<?
htmlFoot();
?>
