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

$dbc = \sys::dbCheck('keyregister');

$dbc->defineField('keyset', 'varchar');
$dbc->defineField('keyset_type', 'int');
$dbc->defineField('location', 'varchar', 100);
$dbc->defineField('properties_id', 'bigint');
$dbc->defineField('people_id', 'bigint');
$dbc->defineField('description', 'text');
$dbc->defineField('archived', 'tinyint');
$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');

$dbc->defineIndex('idx_keyregister_keyset_type_properties_id', '`keyset_type` ASC, `properties_id` ASC');

$dbc->check();
