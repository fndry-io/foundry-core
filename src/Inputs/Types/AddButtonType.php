<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class AddButtonType
 *
 * @package Foundry\Requests\Types
 */
class AddButtonType extends ButtonType {

	public function __construct(
		string $name,
		string $label,
		string $action = null,
		string $title = null,
		array $query = [],
		string $method = 'GET',
		string $id = null
	) {
		$type = 'add';
		parent::__construct( $label, $action, $title, $query, $method, $id, $type );
	}

}
