#!/bin/bash

EXECUTABLE="/usr/local/mysql-standard-5.0.27-osx10.4-i686/bin/mysqldump"
#EXECUTABLE="/usr/local/mysql-5.0.45-osx10.4-i686/bin/mysqldump"

cat birdwalker.sql state.sql trip.sql location.sql species.sql sighting.sql taxonomy.sql countyfrequency.sql | /usr/local/mysql/bin/mysql -u birdwalker -pbirdwalker



