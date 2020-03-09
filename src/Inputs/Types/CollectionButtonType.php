<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class CollectionButtonType
 *
 * @package Foundry\Requests\Types
 */
class CollectionButtonType extends ButtonType {

	public function __construct(
		string $label,
        string $type,
		string $title = null,
        string $action = null,
        array $params = [],
        string $method = 'GET',
        string $id = null
	) {
		parent::__construct( $label, $action, $title, $params, $method, $id, $type );
	}

}
