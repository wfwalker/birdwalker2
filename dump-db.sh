#!/bin/bash

EXECUTABLE="/usr/local/mysql-standard-5.0.27-osx10.4-i686/bin/mysqldump"
#EXECUTABLE="/usr/local/mysql-5.0.45-osx10.4-i686/bin/mysqldump"

AUTHFLAGS="-u birdwalker -pbirdwalker birdwalker"
OTHERFLAGS="--extended-insert=FALSE --quote-names=FALSE"

$EXECUTABLE $OTHERFLAGS $AUTHFLAGS states | grep INSERT > state.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS locations | grep INSERT > location.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS sightings | grep INSERT > sighting.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS trips | grep INSERT > trip.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS species | grep INSERT > species.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS taxonomy | grep INSERT > taxonomy.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS countyfrequency | grep INSERT > countyfrequency.sql
