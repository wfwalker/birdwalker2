#!/bin/bash

log() { echo "$@"; echo "$@" >> testresults.txt; }

echo -n "" > testresults.txt

for file in $(ls *.php); do
	log "---- $file ----"
	php ./$file > testout.html
	egrep "(mysql|Fatal error)" testout.html >> testresults.txt
	ec=$?
	if [ $ec -eq 1 ]; then
		log "---- $file PASSED ----"
	else
		log "---- $file FAILED ----"
	fi		

	tidy -html testout.html > /dev/null 2>> testresults.txt
done

exit 0