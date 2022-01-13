<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace cms\keyregister\dao;

$dbc = \sys::dbCheck('properties');

$dbc->defineField('property_manager', 'bigint');
$dbc->defineField('forrent', 'tinyint');

$dbc->check();
