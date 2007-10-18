#!/bin/bash

cat birdwalker.sql state.sql trips.sql location.sql species.sql sightings.sql taxonomy.sql countyfrequency.sql | /usr/local/mysql/bin/mysql -u birdwalker -pbirdwalker



