<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class CancelButtonType
 *
 * @package Foundry\Requests\Types
 */
class LinkButtonType extends ButtonType {

	public function __construct(
		string $label,
		string $action,
		string $title = null,
		array $query = [],
		string $method = 'GET',
		string $id = null
	) {
		$type = 'link';
		parent::__construct( $label, $action, $title, $query, $method, $id, $type );
	}

}
