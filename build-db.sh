#!/bin/bash

cat birdwalker.sql state.sql trip.sql location.sql species.sql sighting.sql taxonomy.sql countyfrequency.sql | /usr/local/mysql/bin/mysql -u birdwalker -pbirdwalker



