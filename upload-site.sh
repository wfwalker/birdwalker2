#!/bin/bash

filesToCopy=$(find . -maxdepth 1 -type f -name \*.php -not -name \*edit\.\* -and -not -name \*create\*)

scp stylesheet.css $filesToCopy walker@sven.spflrc.org:~/.www/
scp images/left.jpg walker@sven.spflrc.org:~/.www/images/
