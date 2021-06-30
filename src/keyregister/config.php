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

class config extends \config {
  const keyregister_db_version = 0.3;

  const enable_qr_codes = false;

  const label = 'Key Register';
  const label_edit = 'Edit Key';
  const label_issue = 'Issue Key';
  const label_issue_log = 'Issue Log';
  const label_new = 'New Key';

  const keyset_management = 1;
  const keyset_tenant = 2;
  const keyset_management_label = 'Management';
  const keyset_tenant_label = 'Tenant';

  static $KEYCHECKOUT = '';
  protected static $_KEYREGISTER_VERSION = 0;

  public static function keyset_abbreviation( int $type) : string {
    if ( self::keyset_management == $type) {
      return 'M';

    }
    elseif ( self::keyset_tenant == $type) {
      return 'T';

    }

    return '?';

  }

  public static function keyset_text( int $type) : string {
    if ( self::keyset_management == $type) {
      return self::keyset_management_label;

    }
    elseif ( self::keyset_tenant == $type) {
      return self::keyset_tenant_label;

    }

    return '?';

  }

  public static function keyregister_checkdatabase() {
    if (self::keyregister_version() < self::keyregister_db_version) {
      self::keyregister_version(self::keyregister_db_version);

      $dao = new dao\dbinfo;
      $dao->dump($verbose = false);

    }
    // sys::logger( 'bro!');

  }

  public static function keyregister_config() {
    return implode(DIRECTORY_SEPARATOR, [
      self::keyregister_Path(),
      'keyregister.json',
    ]);

  }

  public static function keyregister_init() {
    if (file_exists($config = self::keyregister_config())) {
      $j = json_decode(file_get_contents($config));

      if (isset($j->keyregister_version)) {
        self::$_KEYREGISTER_VERSION = (float)$j->keyregister_version;
      }

      if (isset($j->keyregister_keycheckout)) {
        self::$KEYCHECKOUT = $j->keyregister_keycheckout;
      }
      else {
        self::$KEYCHECKOUT = $j->keyregister_keycheckout = md5(time());
        file_put_contents($config, json_encode($j, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

      }
    }
  }

  public static function keyregister_Path(): string {
    $path = implode(DIRECTORY_SEPARATOR, [
      rtrim(self::cmsStore(), '/'),
      'keyregister'

    ]);

    if (!is_dir($path)) {
      mkdir($path);
      chmod($path, 0777);
    }

    return $path;

  }

  static protected function keyregister_version($set = null) {
    $ret = self::$_KEYREGISTER_VERSION;

    if ((float)$set) {
      $config = self::keyregister_config();

      $j = file_exists($config) ?
        json_decode(file_get_contents($config)) :
        (object)[];

      self::$_KEYREGISTER_VERSION = $j->keyregister_version = $set;

      file_put_contents($config, json_encode($j, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    }

    return $ret;

  }

}

config::keyregister_init();
