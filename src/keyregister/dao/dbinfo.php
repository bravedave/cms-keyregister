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

use sys;

class dbinfo extends \dao\_dbinfo {
	/*
	 * it is probably sufficient to copy this file into the <application>/app/dao folder
	 *
	 * from there store you structure files in <application>/dao/db folder
	 */
	protected function check() {

		parent::check();
		parent::checkDIR(__DIR__);
	}

	public function setVersion(string $key, float $version): void {

		$json = (object)[];
		if (file_exists($store = $this->db_version_file())) {
			$json = json_decode(file_get_contents($store));
		}

		$json->{$key} = $version;

		file_put_contents($store, json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		if (posix_geteuid() == fileowner($store)) {
			chmod($store, 0666);
		}
		clearstatcache(true);
	}
}
