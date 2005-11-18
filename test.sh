#!/bin/bash

log() { echo "$@"; echo "$@" >> testresults.txt; }

echo -n "" > testresults.txt

baseURL="http://localhost/~walker/birdwalker2/"

successURLs="\
photosneeded.php \
speciesedit.php?speciesid=9022020100 \
sightingedit.php?sightingid=11631 \
tripedit.php?tripid=430 \
countyindex.php \
credits.php \
santaclarayearlist.php?year=2005 \
targetyearbirds.php \
tripcreate.php \
slideshow.php \
slideshow.php?speciesid=8012080300 \
slideshow.php?month=09 \
slideshow.php?year=2004&month=09 \
specieslist.php?locationid=131&month=6&year=2000 \
statedetail.php?view=locationsbymonth&stateid=5 \
statedetail.php?view=locations&stateid=5 \
speciesdetail.php?view=locationsbyyear&speciesid=11061111000 \
yeardetail.php?view=chrono&year=1999 \
yeardetail.php?view=photo&year=2004 \
speciesindex.php?view=chrono \
countydetail.php?county=Santa+Clara&stateid=5 \
countydetail.php?view=photo&county=Santa+Clara&stateid=5 \
countyindex.php \
familydetail.php?familyid=2227 \
index.php \
tripdetail.php?tripid=1 \
tripdetail.php?tripid=100 \
locationdetail.php?locationid=70 \
locationindex.php \
monthdetail.php?view=trips&year=2005&month=03 \
onthisdate.php \
orderdetail.php?orderid=22 \
photodetail.php?sightingid=8224 \
photoindex.php \
photoindexlocation.php \
photoindextaxo.php \
slideshow.php \
speciesindex.php \
errorcheck.php \
statedetail.php?stateid=5 \
statedetail.php?stateid=5&view=photo \
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
locationdetail.php?locationid=70&view=pants \
locationdetail.php?locationid=pants \
orderdetail.php \
orderdetail.php?order=22 \
photodetail.php \
statedetail.php \
statedetail.php?ssdftateid=5 \
tripdetail.php \
yeardetail.php \
"

echo "" > detailpages.html

for successURL in $successURLs; do
	curl -s $baseURL$successURL > detail.html
	cat detail.html >> detailpages.html
	egrep "(Notice|404 Not Found|mysql|Fatal error|Parse error|Warning)" detail.html >> testresults.txt
	ec=$?

	tidy -e detail.html > /dev/null 2>> testresults.txt

	if [ $ec -eq 1 ]; then
		log "passed $successURL"
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
		log "passed $failureURL (NEG test)"
	else
		log "FAILED $failureURL (NEG test)"
	fi		
done

log ""
log "------------------------------------"
log ""

exit 0

# to find delimiters
# grep class detailpages.html | sed 's/.*class=["]*//g' | sed 's/[>"]/ /g' | cut -f 1 -d' ' | sort -u
