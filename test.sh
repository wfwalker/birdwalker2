#!/bin/bash

log() { echo "$@"; echo "$@" >> testresults.txt; }

echo -n "" > testresults.txt

for file in $(ls *.php); do
	log "---- $file ----"
	php ./$file > $file.html
	egrep "(mysql|Fatal error)" $file.html >> testresults.txt
	ec=$?

	tidy -e $file.html > /dev/null 2>> testresults.txt

	if [ $ec -eq 1 ]; then
		log "---- $file PASSED ----"
	else
		log "---- $file FAILED ----"
	fi		

	log ""
	log ""
done

exit 0