<?php

include_once("Flickr/API.php");
include_once("XML/Tree.php");

function insertFlickrTripLink($tripInfo)
{
  $tripYear = substr($tripInfo["Date"], 0, 4);
  $tripMonth = substr($tripInfo["Date"], 5, 2);
  $tripDay = substr($tripInfo["Date"], 8, 2);

# bail if the classes are missing

  if (! class_exists("Flickr_API")) return;

# create a new api object

  $api =& new Flickr_API(array(
							   'api_key'  => '4ca581be1b25f2ca7eeeb84e677ba2b9',
							   'api_secret' => '6617d9306f5e7d47',
							   ));

#$response = $api->callMethod('flickr.auth.getToken', array('frob' => '7338944-6cf9f7b03ad063cb',));
#  $response = $api->callMethod('flickr.auth.getFrob');

  $tempo = $tripInfo["startTimestamp"] . "," . ($tripInfo["stopTimestamp"]);

   $response = $api->callMethod('flickr.photos.getCounts', array(
																'auth_token' => '2147715-1d3462c2eae2f491',
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
	//    echo "<p><a href=\"". $api->getAuthUrl("read", "7338825-f918b78d80dee9a8") . "\">login</a></p>";
  }

  if ($count > 0)
  {
	echo "<div class=\"heading\"><a href=\"http://www.flickr.com/photos/billwalker/archives/date-taken/" . $tripYear . "/" . $tripMonth . "/" . $tripDay . "/\">";

	echo "photo"; if ($count > 1) { echo "s"; }
	echo " at flickr...</a></div>";
  }
}

?>
