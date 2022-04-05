<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace cms\keyregister\dao\dto;

use dvc\dao\dto\_dto;

class keyregister extends _dto {
	public $id = 0;

	public $keyset = '';
	public $keyset_type = 0;
	public $location = '';
	public $properties_id = 0;
	public $address_street = '';
	public $people_id = 0;
	public $name = '';	// of person who has the key
	public $mobile = '';	// of person who has the key
	public $description = '';
	public $img_version = 0;
	public $created = '';
	public $updated = '';
	public $haspdf = 'no';

}