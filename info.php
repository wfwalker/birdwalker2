<?php 
// we have to make our XML declaration this way or
// the some servers will think it is a PHP command
// instead of an XML command -- we will discuss how
// to tell whether you have this problem later
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"\x3f>";
  echo "\n";
?>

<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 

<html xmlns="http://www.w3.org/1999/xhtml" 
  xml:lang="en" lang="en">

<head>
  <title>My First PHP Generated Document</title>
</head>

<body>

<?php phpinfo(); ?>

  <h2>My First PHP Generated Document</h2>

  <form method="post" action="hello.php" id="form1" name="form1">
    <p>Enter your name here:</p>
    <input type="text" id="namefld" name="namefld" />
    <p>Click this button to create a new page:</p>
    <input type="submit" value="Click Me!" />
  </form>

  <p>This page written by: 
  <cite>(your name here) </cite>
  <br />
  &copy; 2002 and beyond</p>

</body>
</html>
