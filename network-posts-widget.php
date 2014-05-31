<?php

/**
* Plugin Name: Network Posts Widget
* Description: a WordPress network wide post widget that dosen't suck
* Author: Russell Fair
* Version: 0.1
*/

/*
Road Map ...

First Version:
    Must have a widget that displays posts from accross the network
    Must include a global  db table w/ data for each post (much like mu sitewide tags)
    obviously structured similarly to "posts" but will include blog id and possibly more
    Some type of intelegent query and cache that prevents monster queries

Later Versions:
    A sexy front end updater that allows for "real time" updates as they're posted through the network
    Ability to have multiple widgets with different configurations

Beyond that? Who knows
*/

/**
* @todo write some actual unit tests
* @todo run the tests
* @todo write WordPress repository readme and release
* @todo allow for multiple widgets / cache etc
*/

require_once('lib/Core.php');
new NPW\Core;
