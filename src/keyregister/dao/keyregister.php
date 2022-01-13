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

use cms\keyregister\config;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use dao\_dao;
use ParseCsv;

class keyregister extends _dao {
	protected $_db_name = 'keyregister';
	protected $template = __NAMESPACE__ . '\dto\keyregister';

	public function import() {
		$path = implode(
			DIRECTORY_SEPARATOR,
			[
				config::cmsStore(),
				'default-keyregister.csv'

			]

		);

		if ( file_exists( $path)) {
			$csv = new ParseCsv\Csv;
			$csv->auto($path);
			set_time_limit(300);

			\sys::logger(sprintf('<%s> %s', $csv->getTotalDataRowCount(), __METHOD__));
			foreach ($csv->data as $t) {
				if ($t['properties_id']) {
					$a = [
						'keyset' => $t['keyset'],
						'properties_id' => $t['properties_id'],
						'updated' => \db::dbTimeStamp(),
						'created' => \db::dbTimeStamp()

					];

					$a['keyset_type'] = config::keyset_management;
					$this->Insert($a);

					$a['keyset_type'] = config::keyset_tenant;
					$this->Insert($a);
				}
			}

		}
		else {
			\sys::logger( sprintf('<missing import file> %s', $path, __METHOD__));
			\sys::logger( sprintf('<%s> %s', $path, __METHOD__));

		}

	}

	public function getByKeySet(string $key) {
		if ($key) {
			$sql = sprintf(
				'SELECT * FROM `%s` WHERE `keyset` = %s AND `keyset_type` = %s',
				$this->db_name(),
				$this->quote($key),
				$this->quote(config::keyset_management)

			);

			if ($res = $this->Result($sql)) {
				return $res->dto($this->template);
			}
		}

		return null;
	}

	public function getDataSet( $archived = false) {
		$where = '';
		$_where = [];
		if (!$archived) $_where[] = 'k.`archived` = 0';

		if ( $_where) {
			$where = sprintf(
				'WHERE %s',
				implode( ' AND ', $_where)

			);
		}

		$sql = sprintf(
			'SELECT
				k.*,
				prop.`address_street`,
				prop.`property_manager`,
				prop.`forrent`,
				p.`name`,
				p.`mobile`,
				kl.`maxdate`,
				CASE
				WHEN prop.`property_manager` > 0 THEN u.name
				WHEN cp.`PropertyManager` != "" THEN uc.name
				ELSE ""
				END pm
			FROM
				`%s` k
					LEFT JOIN
				(SELECT
						MAX(date) maxdate, keyregister_id
				FROM
						keyregister_log
				GROUP BY keyregister_id) kl ON kl.`keyregister_id` = k.`id`
					LEFT JOIN
				`properties` prop ON prop.`id` = k.`properties_id`
					LEFT JOIN
				`console_properties` cp ON cp.`properties_id` = k.`properties_id`
					LEFT JOIN
				`people` p ON p.`id` = k.`people_id`
					LEFT JOIN
				`users` u ON prop.`property_manager` = u.`id`
					LEFT JOIN
				`users` uc ON cp.`PropertyManager` = uc.`console_code`
				%s
			ORDER BY
				CAST( k.`keyset` AS INTEGER) ASC, k.`keyset_type` ASC',
			$this->db_name(),
			$where

		);

		return $this->Result($sql);
	}

	public function getImageMimeType(dto\keyregister $dto): string {
		if ($path = $this->getImagePath($dto)) {
			return mime_content_type($path);
		}

		return '';
	}

	public function getImagePath(dto\keyregister $dto): string {
		$target = implode(DIRECTORY_SEPARATOR, [
			$this->getStore($dto),
			'image'

		]);

		if (file_exists($target . '.jpg')) return $target . '.jpg';
		if (file_exists($target . '.png')) return $target . '.png';
		if (file_exists($target . '.pdf')) return $target . '.pdf';
		if (file_exists($target . '.pdf')) return dirname(__DIR__) . '/resource/file-pdf.svg';

		return '';
	}

	public function getImageTime(dto\keyregister $dto): int {
		if ($path = $this->getImagePath($dto)) {
			return filemtime($path);
		}

		return 0;
	}

	public function getKeysForPerson(int $id = 0): array {
		if ($id) {
			$sql = sprintf(
				'SELECT
				k.*,
				prop.`address_street`,
				prop.`street_index`,
				kl.`maxdate` issued
			FROM
				`%s` k
					LEFT JOIN
				(SELECT
						MAX(date) maxdate, keyregister_id
				FROM
						keyregister_log
				GROUP BY keyregister_id) kl ON kl.`keyregister_id` = k.`id`
					LEFT JOIN
				`properties` prop ON prop.`id` = k.`properties_id`
			WHERE
				k.`people_id` = %d
			ORDER BY
				kl.`maxdate` ASC',
				$this->db_name(),
				$id

			);

			if ($res = $this->Result($sql)) {
				return $res->dtoSet();
			}
		}

		return [];
	}

	public function getKeysForProperty(int $id = 0): array {
		if ($id) {
			$sql = sprintf(
				'SELECT
				k.*,
				prop.`address_street`,
				prop.`street_index`,
				people.`name`,
				(SELECT MAX(kl.`date`) maxdate FROM keyregister_log kl WHERE kl.`keyregister_id` = k.`id` AND %s = kl.`description`) %s,
				(SELECT MAX(kl.`date`) maxdate FROM keyregister_log kl WHERE kl.`keyregister_id` = k.`id` AND %s = kl.`description`) %s
			FROM
				`keyregister` k
					LEFT JOIN
				`properties` prop ON prop.`id` = k.`properties_id`
					LEFT JOIN
				`people` ON people.`id` = k.`people_id`
			WHERE
				k.`properties_id` = %d
			ORDER BY
				issued ASC',
				$this->quote('issue'),
				$this->quote('issued'),
				$this->quote('return'),
				$this->quote('returned'),
				$id

			);

			if ($res = $this->Result($sql)) {
				return $res->dtoSet();
			}
		}

		return [];
	}

	public function getQRPath(dto\keyregister $dto): string {
		return implode(DIRECTORY_SEPARATOR, [
			$this->getStore($dto),
			'qr-code.svg'

		]);
	}

	public function getRecordCount($archived = false): int {
		$sql = 'SELECT count(`id`) tot FROM `keyregister`';
		if ( !$archived) $sql .= ' WHERE `archived` = 0';
		if ($res = $this->Result($sql)) {
			if ($dto = $res->dto()) {
				return (int)$dto->tot;
			}
		}

		return 0;
	}

	public function getRichData(dto\keyregister $dto): dto\keyregister {
		if ($dto->properties_id) {
			$_dao = new \dao\properties;
			if ($_dto = $_dao->getByID($dto->properties_id)) {
				$dto->address_street = $_dto->address_street;
			}
		}

		if ($dto->people_id) {
			$_dao = new \dao\people;
			if ($_dto = $_dao->getByID($dto->people_id)) {
				$dto->name = $_dto->name;
				$dto->mobile = $_dto->mobile;
			}
		}

		$dto->img_version = $this->getImageTime($dto);
		$dto->haspdf = 'application/pdf' == $this->getImageMimeType($dto) ? 'yes' : 'no';

		return $dto;
	}

	public function getStore(dto\keyregister $dto): string {
		$path = implode(DIRECTORY_SEPARATOR, [
			config::keyregister_Path(),
			$dto->id

		]);

		if (!is_dir($path)) {
			mkdir($path);
			chmod($path, 0777);
		}

		return $path;
	}

	public function reset() {
		$this->Q('DROP TABLE IF EXISTS `keyregister`');
		$this->Q('DROP TABLE IF EXISTS `keyregister_log`');
		config::keyregister_version_reset();

		$dir = config::keyregister_Path();
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		config::keyregister_checkdatabase();
		$configFile = config::keyregister_config();
		foreach ($files as $fileinfo) {

			if ($fileinfo->getRealPath() == $configFile) continue;

			$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
			$todo($fileinfo->getRealPath());

		}

		config::keyregister_checkdatabase();
	}
}
