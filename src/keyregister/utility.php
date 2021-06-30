<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace cms\keyregister;

use application;
use cms;
use dvc\service;
use green;

class utility extends service {
  protected function _upgrade() {
    config::route_register(config::$KEYCHECKOUT, 'cms\\keyregister\\keycheckout');
    config::route_register('keyregister', 'cms\\keyregister\\controller');

    config::keyregister_checkdatabase();

    cms\console\config::cms_console_checkdatabase();

    green\baths\config::green_baths_checkdatabase();
    green\beds_list\config::green_beds_list_checkdatabase();
    green\people\config::green_people_checkdatabase();
    green\properties\config::green_properties_checkdatabase();
    green\property_diary\config::green_property_diary_checkdatabase();
    green\property_type\config::green_property_type_checkdatabase();
    green\postcodes\config::green_postcodes_checkdatabase();
    green\users\config::green_users_checkdatabase();


    echo (sprintf('%s : %s%s', 'updated', __METHOD__, PHP_EOL));
  }

  protected function _upgrade_dev() {
    // config::route_register('people', '');
    // config::route_register('properties', 'green\\properties\\controller');
    config::route_register('beds', 'green\\beds_list\\controller');
    config::route_register('baths', 'green\\baths\\controller');
    config::route_register('property_type', 'green\\property_type\\controller');
    config::route_register('postcodes', 'green\\postcodes\\controller');
    config::route_register('users', 'green\\users\\controller');

    // config::route_register('offertolease', 'dvc\\offertolease\\app');
    // config::route_register('otl', 'dvc\\offertolease\\otlclient');
    // config::route_register('banklink', 'cms\\banklink\controller');
    // config::route_register('sms', 'sms\\controller');

    echo (sprintf('%s : %s%s', 'updated (dev)', __METHOD__, PHP_EOL));
  }

  static function upgrade() {
    $app = new self(application::startDir());
    $app->_upgrade();
  }

  static function upgrade_dev() {
    $app = new self(application::startDir());
    $app->_upgrade_dev();
  }
}
