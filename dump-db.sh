mysqldump -u birdwalker -pbirdwalker birdwalker location | grep INSERT > location.sql
mysqldump -u birdwalker -pbirdwalker birdwalker sighting | grep INSERT > sighting.sql
mysqldump -u birdwalker -pbirdwalker birdwalker trip | grep INSERT > trip.sql
mysqldump -u birdwalker -pbirdwalker birdwalker species | grep INSERT > species.sql
mysqldump -u birdwalker -pbirdwalker birdwalker taxonomy | grep INSERT > taxonomy.sql
mysqldump -u birdwalker -pbirdwalker birdwalker countyfrequency | grep INSERT > countyfrequency.sql
