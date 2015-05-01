CUHK CSCI2720 Project -- Polling website
==========================================

## Introduction
A php/mariadb/html5 website support polling

## CREDITS
Some 3rd party libraries are used:
* The PHP Login Project -- handling login/logout
* MeekroDB -- prevent sql injection and neat code
* Twitter bootstrap -- frontend ui design
* FileSaver.js -- export voters as file

## Installation

Create a database and tables via the SQL statements in the `_install` folder.
Change mySQL database user and password in `config/db.php` (*DB_USER* and *DB_PASS*).

1. clone / download this repo
2. change database settings in config/db.php
3. run the sql statements in \_installation

### manager
- [X] login/logout
- [X] add event
- [X] list event
- [X] add options
  - [ ] option with image
- [lib only] show options
- [ ] sort event
- [lib only] delete event
- [ ] activates event
- [X] edit voters
- [X] export voters
- [X] import voters
- [ ] view result

### voter
- [x] view options
- [X] vote
- [X] view result

## Bug 蟲蟲
- event time is delayed by 1 month (how strange!)
- many security problems (I will concentrateon features for now)

## License

Licensed under [MIT](http://www.opensource.org/licenses/mit-license.php).
You can use this script for free for any private or commercial projects.
