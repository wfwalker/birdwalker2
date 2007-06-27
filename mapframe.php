<?php

require_once("./birdwalker.php");
require_once("./request.php");

$request = new Request;
echo "<!DOCTYPE  HTML PUBLIC  \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
?>

<html>
  <head>
    <link rel="alternate" type="application/atom+xml" title="Atom" href="./indexrss.php" />
    <link rel="SHORTCUT ICON" href="./images/favicon.ico">
    <link title="Style" href="./stylesheet.css" type="text/css" rel="stylesheet">
    <title>birdWalker | <?= $title ?></title>
  </head>

  <body>
<?

$map = new Map("./" . $request->getPageScript(), $request);
$map->drawGoogle(true);

htmlFoot();
?>
