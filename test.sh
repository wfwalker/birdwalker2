#!/bin/bash

log() { echo "$@"; echo "$@" >> testresults.txt; }

echo -n "" > testresults.txt

for file in $(ls *.php); do
	log "---- $file ----"
	time php ./$file | egrep "(mysql|Fatal error)" >> testresults.txt
	ec=$?
	if [ $ec -eq 1 ]; then
		log "---- $file PASSED ----"
	else
		log "---- $file FAILED ----"
	fi		
done

exit 0