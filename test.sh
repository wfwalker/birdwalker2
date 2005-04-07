#!/bin/bash

log() { echo "$@"; echo "$@" >> testresults.txt; }

echo -n "" > testresults.txt

baseURL="http://localhost/~walker/birdwalker2/"

successURLs="\
chronocayearlist.php?year=1999 \
chronolifelist.php \
countydetail.php?county=Santa+Clara&stateid=5 \
countyindex.php \
familydetail.php?family=2227 \
index.php \
locationdetail.php?locationid=70 \
locationindex.php \
monthdetail.php?view=trip&year=2005&month=03 \
onthisdate.php \
orderdetail.php?order=22 \
photodetail.php?id=8224 \
photoindex.php \
photoindexlocation.php \
photoindextaxo.php \
slideshow.php \
speciesindex.php \
statedetail.php?stateid=5 \
stateindex.php \
targetyearbirds.php \
tripindex.php \
yeardetail.php?year=1999 \
"

failureURLs="\
chronocayearlist.php \
countydetail.php \
familydetail.php \
locationdetail.php \
monthdetail.php \
orderdetail.php \
photodetail.php \
statedetail.php \
statedetail.php?ssdftateid=5 \
tripdetail.php \
yeardetail.php \
"

log ""
log "----------- OTHER PAGES ---------------"
log ""

echo "" > detailpages.txt

for successURL in $successURLs; do
	curl -s $baseURL$successURL > detail.html
	egrep "(404 Not Found|mysql|Fatal error)" detail.html >> testresults.txt
	ec=$?

	tidy -e detail.html > /dev/null 2>> testresults.txt

	if [ $ec -eq 1 ]; then
		log "PASSED $successURL"
	else
		log "FAILED $successURL"
	fi		
done

for failureURL in $failureURLs; do
	curl -s $baseURL$failureURL > detail.html
	egrep "(404 Not Found|mysql|Fatal error)" detail.html >> testresults.txt
	ec=$?

	tidy -e detail.html > /dev/null 2>> testresults.txt

	if [ $ec -eq 0 ]; then
		log "PASSED $failureURL (NEG test)"
	else
		log "FAILED $failureURL (NEG test)"
	fi		
done

log ""
log "------------------------------------"
log ""

exit 0