#!/bin/bash

echo "Uploading SQL"
rsync -u --progress -r -e ssh *.sql sven.spflrc.org:/home/walker/

echo "Building DB on SPFLRC"
ssh walker@sven.spflrc.org "cat birdwalker.sql state.sql trip.sql location.sql county.sql species.sql sighting.sql taxonomy.sql| mysql -u walker birdwalker --password=walker"

