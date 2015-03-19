CUHK CSCI2720 Project -- Polling website
==========================================

## Introduction
A php/mariadb/html5 website support polling

## CREDITS
Some 3rd party libraries are used:
* The PHP Login Project
* Twitter bootstrap

## Installation

Create a database *login* and the table *users* via the SQL statements in the `_install` folder.
Change mySQL database user and password in `config/db.php` (*DB_USER* and *DB_PASS*).

1. clone / download this repo
2. change database settings in config/db.php
3. run the sql statements in \_installation

## TODO

### manager
- [X] login/logout
- [ ] add event
- [ ] list event
- [ ] edit event
- [ ] delete event
- [ ] activates event

### voter
- [ ] list active event
- [ ] vote

## License

Licensed under [MIT](http://www.opensource.org/licenses/mit-license.php). You can use this script for free for any
private or commercial projects.
