#!/bin/bash

filesToCopy=$(find . -type f -name \*.php -not -name \*edit\.\* -and -not -name speciesindex2.php -and -not -name photosneeded.php -and -not -name targetyearbirds.php -and -not -name \*create\*)

scp stylesheet.css $filesToCopy walker@sven.spflrc.org:~/.www/
