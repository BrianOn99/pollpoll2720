CUHK CSCI2720 Project -- Polling website
==========================================

## Introduction
A php/mariadb/html5 website support polling

## Requirements
It is only tested on:
- php5.5.9, may not work for php<5.4
- arch/ubuntu linux
- apache/nginx
- firefox>36/chrome>40

## CREDITS
Some 3rd party libraries are used:
* The PHP Login Project -- handling login/logout
* MeekroDB -- prevent sql injection and neat code
* Twitter bootstrap -- frontend ui design
* [FileSaver.js](https://github.com/eligrey/FileSaver.js/) -- export voters as file
* canvasjs -- display vote summary
* [tablesort](http://tristen.ca/tablesort/demo/) -- sort event table for manager 

## Installation

Create a database and tables via the SQL statements in the `_install` folder.
Change mySQL database user and password in `config/db.php` (`DB_USER` and `DB_PASS`).

1. clone / download this repo
2. change database settings in config/db.php
3. run the sql statements in \_installation
4. give "choice_img" directory write permission web server (apache/nginx, etc)

### manager
- [X] login/logout
- [X] add event
- [X] list event
- [X] add options
  - [X] option with image
- [X] sort event
- [X] activates event
- [X] edit voters
- [X] export voters
- [X] import voters
- [X] view result

### voter
- [x] view options
- [X] vote
- [X] view result

## Bug 蟲蟲
- many security problems

## License

Licensed under [MIT](http://www.opensource.org/licenses/mit-license.php).
You can use this script for free for any private or commercial projects.
