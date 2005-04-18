#!/bin/bash

log() { echo "$@"; echo "$@" >> testresults.txt; }

echo -n "" > testresults.txt

baseURL="http://localhost/~walker/birdwalker2/"

successURLs="\
speciesdetail.php?view=byyear&speciesid=11061111000 \
yeardetail.php?view=chrono&year=1999 \
yeardetail.php?view=photo&year=2004 \
speciesindex.php?view=chrono \
countydetail.php?county=Santa+Clara&stateid=5 \
countydetail.php?view=photo&county=Santa+Clara&stateid=5 \
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
errorcheck.php \
statedetail.php?stateid=5 \
stateindex.php \
targetyearbirds.php \
tripindex.php \
yeardetail.php?year=1999 \
"

failureURLs="\
countydetail.php \
familydetail.php \
locationdetail.php \
locationdetail.php?location=23 \
locationdetail.php?locationid=pants \
monthdetail.php \
orderdetail.php \
photodetail.php \
statedetail.php \
statedetail.php?ssdftateid=5 \
tripdetail.php \
yeardetail.php \
"

echo "" > detailpages.txt

for successURL in $successURLs; do
	curl -s $baseURL$successURL > detail.html
	egrep "(404 Not Found|mysql|Fatal error|Parse error)" detail.html >> testresults.txt
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