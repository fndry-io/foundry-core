<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class CancelButtonType
 *
 * @package Foundry\Requests\Types
 */
class CancelButtonType extends ButtonType {

	public function __construct(
		string $label,
		string $action = null,
		string $title = null,
		array $query = [],
		string $method = 'GET',
		string $id = null
	) {
		$type = 'cancel';
		parent::__construct( $label, $action, $title, $query, $method, $id, $type );
	}

}
