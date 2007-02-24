<?php

include_once("Flickr/API.php");
include_once("XML/Tree.php");

function insertFlickrTripLink($tripInfo)
{

# bail if the classes are missing

  if (! class_exists("Flickr_API")) return;
  if (! class_exists("XML::Tree")) return;

# create a new api object

  $api =& new Flickr_API(array(
							   'api_key'  => '4ca581be1b25f2ca7eeeb84e677ba2b9',
							   'api_secret' => '6617d9306f5e7d47',
							   ));

# $response = $api->callMethod('flickr.auth.getToken', array('frob' => '6787850-db993b0f356a3f2f',));

  $tempo = $tripInfo["startTimestamp"] . "," . ($tripInfo["stopTimestamp"]);

  $response = $api->callMethod('flickr.photos.getCounts', array(
																'auth_token' => '1937122-c58dfbff3a3343ac',
																'taken_dates' => $tempo));

# check the response

  $count = 0;
  
  if ($response) { # response is an XML_Tree root object
	echo urlencode($response->dump());
	$obj = $response->getElement(array(1,1));
	$count = $obj->getAttribute("count");
  } else { # fetch the error
	$code = $api->getErrorCode();
	$message = $api->getErrorMessage();
	echo "<!-- flickr down: " . $code . " " . $message . " -->";
    # echo "<p><a href=\"". $api->getAuthUrl("read", "") . "\">login</a></p>";
  }

  if ($count > 0)
  {
	echo "<p><a href=\"http://www.flickr.com/photos/billwalker/archives/date-taken/" .
	  $tripYear . "/" . $tripMonth . "/" . $tripDay . "/\">" . $count . " flickr photos</a></p>";
  }
}

?>
