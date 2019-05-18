<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class ResetButtonType
 *
 * @package Foundry\Requests\Types
 */
class ResetButtonType extends ButtonType {

	public function __construct(
		string $label,
		string $action = null,
		string $title = null,
		array $query = [],
		string $method = 'GET',
		string $id = null
	) {
		$type = 'reset';
		parent::__construct( $label, $action, $title, $query, $method, $id, $type );
	}

}
