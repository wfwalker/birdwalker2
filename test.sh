#!/bin/bash

log() { echo "$@"; echo "$@" >> testresults.txt; }

echo -n "" > testresults.txt

log ""
log "----------- INDICES ---------------"
log ""

for file in $(ls *index.php); do
#	log "---- $file ----"
	php ./$file > $file.html
	egrep "(mysql|Fatal error)" $file.html >> testresults.txt
	ec=$?

	tidy -e $file.html > /dev/null 2>> testresults.txt

	if [ $ec -eq 1 ]; then
		log "---- $file PASSED ----"
	else
		log ""
		log "---- $file FAILED ----"
		log ""
	fi		
done

exit 0