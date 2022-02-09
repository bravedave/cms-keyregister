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
  const keyregister_db_version = 0.74;

  const enable_qr_codes = false;

  const label = 'Key Register';
  const label_edit = 'Edit Key';
  const label_issue = 'Issue Key';
  const label_issue_log = 'Issue Log';
  const label_new = 'New Key';
  const label_freeset = 'Free Set';

  const keyset_management = 1;
  const keyset_tenant = 2;
  const keyset_management_label = 'Management';
  const keyset_tenant_label = 'Tenant';

  static $KEYCHECKOUT = '';

  public static function keyset_abbreviation(int $type): string {
    if (self::keyset_management == $type) {
      return 'M';
    } elseif (self::keyset_tenant == $type) {
      return 'T';
    }

    return '?';
  }

  public static function keyset_text(int $type): string {
    if (self::keyset_management == $type) {
      return self::keyset_management_label;
    } elseif (self::keyset_tenant == $type) {
      return self::keyset_tenant_label;
    }

    return '?';
  }

  public static function keyregister_checkdatabase() {
    $dao = new dao\dbinfo(null, method_exists(__CLASS__, 'cmsStore') ? self::cmsStore() : self::dataPath());
    // // $dao->debug = true;
    $dao->checkVersion('keyregister', self::keyregister_db_version);
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

      if (isset($j->keyregister_keycheckout)) {
        self::$KEYCHECKOUT = $j->keyregister_keycheckout;
      } else {
        self::$KEYCHECKOUT = $j->keyregister_keycheckout = md5(time());
        file_put_contents($config, json_encode($j, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
      }
    }
  }

  public static function keyregister_Path(): string {
    $path = implode(DIRECTORY_SEPARATOR, [
      rtrim(method_exists(__CLASS__, 'cmsStore') ? self::cmsStore() : self::dataPath(), '/'),
      'keyregister'

    ]);

    if (!is_dir($path)) {
      mkdir($path);
      chmod($path, 0777);
    }

    return $path;
  }

  public static function keyregister_version_reset(): void {
    $dao = new dao\dbinfo(null, method_exists(__CLASS__, 'cmsStore') ? self::cmsStore() : self::dataPath());
    // // $dao->debug = true;
    $dao->setVersion('keyregister', 0);
  }

}

config::keyregister_init();
