#!/bin/bash

echo "birdwalker" && (php ./birdwalker.php || (echo "birdwalker ERROR" && exit 1))
echo "tripindex" && php ./tripindex.php | grep -q error && echo "tripindex ERROR" && exit 1
echo "speciesindex" && php ./speciesindex.php | grep -q error && echo "speciesindex ERROR" && exit 1
echo "locationindex" && php ./locationindex.php | grep -q error && echo "locationindex ERROR" && exit 1
echo "countyindex" && php ./countyindex.php | grep -q error && echo "countyindex ERROR" && exit 1
echo "speciesdetail" && curl -s http://localhost/~walker/birdwalker2/speciesdetail.php?id=3020060500 | grep error && echo "speciesdetail ERROR" && exit 1
echo "tripdetail" && curl -s http://localhost/~walker/birdwalker2/tripdetail.php?id=45 | grep error && echo "tripdetail ERROR" && exit 1
echo "locationdetail" && curl -s http://localhost/~walker/birdwalker2/locationdetail.php?id=36 | grep error && echo "locationdetail ERROR" && exit 1
echo "familydetail" && curl -s http://localhost/~walker/birdwalker2/familydetail.php?id=201 | grep error && echo "familydetail ERROR" && exit 1
echo "orderdetail" && curl -s http://localhost/~walker/birdwalker2/orderdetail.php?id=2 | grep error && echo "orderdetail ERROR" && exit 1
echo "countydetail" && curl -s http://localhost/~walker/birdwalker2/countydetail.php?county=Imperial | grep error && echo "countydetail ERROR" && exit 1
echo "statedetail" && curl -s http://localhost/~walker/birdwalker2/statedetail.php?state=CA | grep error && echo "statedetail ERROR" && exit 1
echo "yeardetail" && curl -s http://localhost/~walker/birdwalker2/yeardetail.php?year=1999 | grep error && echo "yeardetail ERROR" && exit 1

echo "everything ok"

exit 0