
<?php

require("./birdwalker.php");

$theYear = $_GET["year"];

$yearCount = getSpeciesCount("species.Abbreviation=sighting.SpeciesAbbreviation and year(sighting.TripDate)='" . $theYear . "'");
?>

<html>
  <head>
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?php echo $theYear ?> Report</title>
  </head>
  <body>

<?php navigationHeader() ?>

    <div class=contentright>
      <div class="titleblock">	  
        <div class=pagetitle> <?php echo $theYear ?> List</div>
        <div class=pagesubtitle><?php echo $yearCount ?> species</div>
      </div>

<table cell-padding=0 columns=13 class="report-content" width="100%">

<?

$gridQueryString = "select distinct(CommonName), species.objectid as speciesid, bit_or(1 << Month(TripDate)) as mask
    from sighting, species where sighting.SpeciesAbbreviation=species.Abbreviation and year(sighting.TripDate)='" . $theYear . "'
    group by sighting.SpeciesAbbreviation order by speciesid";

$gridQuery = performQuery($gridQueryString);

while ($info = mysql_fetch_array($gridQuery))
{
	$orderNum =  floor($info["speciesid"] / pow(10, 9));
	$theMask = $info["mask"];

	if ($prevOrderNum != $orderNum)
    {
      $orderInfo = getOrderInfo($info["speciesid"]);
      echo "<tr class=\"titleblock\"><td>" . $orderInfo["CommonName"] . "</td>
  <td align=center>Jan</td>
  <td align=center>Feb</td>
  <td align=center>Mar</td>
  <td align=center>Apr</td>
  <td align=center>May</td>
  <td align=center>Jun</td>
  <td align=center>Jul</td>
  <td align=center>Aug</td>
  <td align=center>Sep</td>
  <td align=center>Oct</td>
  <td align=center>Nov</td>
  <td align=center>Dec</td>
            </tr>";
    }


	echo "<tr>
              <td class=firstcell><a href=\"./speciesdetail.php?id=" . $info["speciesid"] . "\">" . $info["CommonName"] . "</a></td> 
              <td align=center>" . bitToString($theMask, 1) . "</td>
              <td align=center>" . bitToString($theMask, 2) . "</td>
              <td align=center>" . bitToString($theMask, 3) . "</td>
              <td align=center>" . bitToString($theMask, 4) . "</td>
              <td align=center>" . bitToString($theMask, 5) . "</td>
              <td align=center>" . bitToString($theMask, 6) . "</td>
              <td align=center>" . bitToString($theMask, 7) . "</td>
              <td align=center>" . bitToString($theMask, 8) . "</td>
              <td align=center>" . bitToString($theMask, 9) . "</td>
              <td align=center>" . bitToString($theMask, 10) . "</td>
              <td align=center>" . bitToString($theMask, 11) . "</td>
              <td align=center>" . bitToString($theMask, 12) . "</td>
           </tr>";
    $prevOrderNum = $orderNum;
}


?>

</table>

    </div>
  </body>
</html>
