echo "select concat('images/photo/', TripDate, '-', SpeciesAbbreviation, '.jpg') from sighting where Photo='1'" | mysql -u birdwalker -pbirdwalker birdwalker | sort > databaselist.txt
echo "select concat('images/thumb/', TripDate, '-', SpeciesAbbreviation, '.jpg') from sighting where Photo='1'" | mysql -u birdwalker -pbirdwalker birdwalker | sort > databaselist2.txt

find images/photo | sort > folderlist.txt
find images/thumb | sort > folderlist2.txt

echo "PHOTO folder"
diff --ignore-case databaselist.txt folderlist.txt

echo "THUMB folder"
diff --ignore-case databaselist2.txt folderlist2.txt

rm -f folderlist.txt folderlist2.txt databaselist.txt databaselist2.txt
