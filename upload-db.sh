#!/bin/bash

echo "Uploading SQL"
rsync -u --progress -r -e ssh *.sql sven.spflrc.org:/home/walker/

echo "Building DB on SPFLRC"
ssh walker@sven.spflrc.org "cat birdwalker.sql state.sql trips.sql location.sql species.sql sightings.sql taxonomy.sql| mysql -u walker birdwalker --password=walker"

