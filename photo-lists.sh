echo "select concat('images/photo/', TripDate, '-', SpeciesAbbreviation, '.jpg') from sighting where Photo='1'" | mysql -u birdwalker -pbirdwalker birdwalker | sort > databaselist.txt

find images/photo | sort > folderlist.txt

diff --ignore-case databaselist.txt folderlist.txt