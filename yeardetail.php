
<?php

require("./birdwalker.php");

$theYear = $_GET["year"];

$yearCount = getSpeciesCount("sighting.Exclude='0' and species.Abbreviation=sighting.SpeciesAbbreviation and year(sighting.TripDate)='" . $theYear . "'");
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $theYear ?> Report</title>
  </head>
  <body>

<?php navigationHeader(); navigationButtons("./yeardetail.php?year=", $theYear, 1996, $theYear - 1, $theYear + 1, 2004); ?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle> <?php echo $theYear ?> List</div>
        <div class=pagesubtitle><?php echo $yearCount ?> species</div>
      </div>

<table cell-padding=0 cellpadding=0 cellspacing=0 columns=13 class="report-content" width="100%">

<tr><td></td>
  <td class=yearcell align=center>Jan</td>
  <td class=yearcell align=center>Feb</td>
  <td class=yearcell align=center>Mar</td>
  <td class=yearcell align=center>Apr</td>
  <td class=yearcell align=center>May</td>
  <td class=yearcell align=center>Jun</td>
  <td class=yearcell align=center>Jul</td>
  <td class=yearcell align=center>Aug</td>
  <td class=yearcell align=center>Sep</td>
  <td class=yearcell align=center>Oct</td>
  <td class=yearcell align=center>Nov</td>
  <td class=yearcell align=center>Dec</td>
            </tr>

<?

$gridQueryString = "select distinct(CommonName), species.objectid as speciesid, bit_or(1 << Month(TripDate)) as mask
    from sighting, species where sighting.Exclude='0' and sighting.SpeciesAbbreviation=species.Abbreviation and year(sighting.TripDate)='" . $theYear . "'
    group by sighting.SpeciesAbbreviation order by speciesid";

$gridQuery = performQuery($gridQueryString);

while ($info = mysql_fetch_array($gridQuery))
{
	$orderNum =  floor($info["speciesid"] / pow(10, 9));
	$theMask = $info["mask"];


	if ($prevOrderNum != $orderNum)
    {
		$orderInfo = getOrderInfo($info["speciesid"]);
		echo "<tr><td class=titleblock colspan=13>" . $orderInfo["CommonName"] . "</td></tr>";
    }

	echo "<tr><td class=firstcell><a href=\"./speciesdetail.php?id=" . $info["speciesid"] . "\">" . $info["CommonName"] . "</a></td>";
	for ($index = 1; $index <= 12; $index++) echo "<td class=bordered align=center>" . bitToString($theMask, $index) . "</td>";
    echo "</tr>";
    $prevOrderNum = $orderNum;
}


?>

</table>

    </div>
  </body>
</html>
