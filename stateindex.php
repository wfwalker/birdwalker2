
<?php

require("/Users/walker/Sites/birdwalker/birdwalker.php");

$stateName = $_GET["state"];
$whereClause =  "species.Abbreviation=sighting.SpeciesAbbreviation and sighting.LocationName=location.Name and location.State='" . $stateName . "'";
$stateListCount = getFancySpeciesCount($whereClause);
$stateListQuery = getFancySpeciesQuery($whereClause);

?>

<html>
  <head>
    <link title="Style" href="/~walker/birdwalker/stylesheet.css" type="text/css" rel="stylesheet">
	  <title>birdWalker | <?php echo $stateName ?> State List</title>
  </head>

  <body>

<?php navigationHeader() ?>

    <div class=contentright>
	  <div class="titleblock">
        <div class=pagetitle><?php echo $stateName ?> State List</div>
        <div class=pagesubtitle> <?php echo $stateListCount ?> species</div>
      </div>
		
<?php formatSpeciesList($stateListCount, $stateListQuery); ?>

    </div>
  </body>
</html>
