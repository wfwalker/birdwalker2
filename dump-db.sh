#!/bin/bash

EXECUTABLE="mysqldump"
AUTHFLAGS="-u birdwalker -pbirdwalker birdwalker"
OTHERFLAGS="--extended-insert=FALSE --quote-names=FALSE"

$EXECUTABLE $OTHERFLAGS $AUTHFLAGS state | grep INSERT > state.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS location | grep INSERT > location.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS sighting | grep INSERT > sightings.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS trip | grep INSERT > trips.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS species | grep INSERT > species.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS taxonomy | grep INSERT > taxonomy.sql
$EXECUTABLE $OTHERFLAGS $AUTHFLAGS countyfrequency | grep INSERT > countyfrequency.sql
