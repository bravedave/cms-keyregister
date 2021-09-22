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

$dbc = \sys::dbCheck('keyregister_log');

$dbc->defineField('keyregister_id', 'bigint');
$dbc->defineField('people_id', 'bigint');
$dbc->defineField('description', 'varchar');
$dbc->defineField('date', 'datetime');

$dbc->defineIndex('idx_keyregister_id', 'keyregister_id');

$dbc->check();
