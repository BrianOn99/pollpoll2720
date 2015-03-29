CUHK CSCI2720 Project -- Polling website
==========================================

## Introduction
A php/mariadb/html5 website support polling

## CREDITS
Some 3rd party libraries are used:
* The PHP Login Project -- handling login/logout
* MeekroDB -- prevent sql injection and neat code
* Twitter bootstrap -- frontend ui design

## Installation

Create a database *login* and the table *users* via the SQL statements in the `_install` folder.
Change mySQL database user and password in `config/db.php` (*DB_USER* and *DB_PASS*).

1. clone / download this repo
2. change database settings in config/db.php
3. run the sql statements in \_installation

## TODO

### manager
- [X] login/logout
- [X] add event
- [X] list event
- [ ] sort event
- [lib only] delete event
- [ ] activates event
- [ ] edit voters
- [ ] export voters
- [ ] import voters

### voter
- [ ] view options
- [ ] vote
- [ ] view result

## Bug 蟲蟲
- event time set to zero (need to convert to mysql syntax)
- many security problems (I will concentrateon features for now)

## License

Licensed under [MIT](http://www.opensource.org/licenses/mit-license.php). You can use this script for free for any
private or commercial projects.
