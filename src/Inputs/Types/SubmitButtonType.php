<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class Type
 *
 * @package Foundry\Requests\Types
 */
class SubmitButtonType extends ButtonType {

	public function __construct(
		string $label,
		string $action,
		string $title = null,
		array $query = [],
		string $method = 'POST',
		string $id = null
	) {
		$type = 'submit';
		parent::__construct( $label, $action, $title, $query, $method, $id, $type );
	}

}
