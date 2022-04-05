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

use dvc\dao\_dao;

class keyregister_log extends _dao {
	protected $_db_name = 'keyregister_log';

	public function getForID( int $id) {
		$sql = sprintf(
			'SELECT
				kl.*,
				p.`name`
			FROM
				`%s` kl
					LEFT JOIN
				people p on p.`id` = kl.`people_id`
			WHERE
				`keyregister_id` = %d
			ORDER BY
				`date` DESC',
			$this->db_name(),
			$id

		);

		return $this->Result($sql);

	}

}