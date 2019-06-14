<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasOptions;
use Foundry\Core\Inputs\Types\Traits\HasQueryOptions;


/**
 * Class AutoCompleteType
 *
 * @package Foundry\Requests\Types
 */
class AutoCompleteInputType extends TextInputType {

	use HasQueryOptions;
	use HasOptions;

	/**
	 * AutoCompleteType constructor.
	 *
	 * @param string $name The field name
	 * @param string $label
	 * @param bool $required
	 * @param string $url The url to fetch the list of available options from
	 * @param array $options The options to display if not doing a url fetch for them
	 * @param null $value
	 * @param string $position
	 * @param string|null $rules
	 * @param string|null $id
	 * @param string|null $placeholder
	 * @param string $query_param
	 */
	public function __construct(
		string $name,
		string $label,
		bool $required = true,
		?string $url,
		array $options = [],
		$value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null,
		string $query_param = 'q'
	) {
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, 'autocomplete' );
		$this->setUrl( $url );
		$this->setQueryParam( $query_param );
	}

	public function display($value = null) {

		if ($value === null) {
			$value = $this->getValue();
		}

		$options = $this->getOptions($value);

		if ( $value === '' || $value === null || ( $this->isMultiple() && empty( $value ) ) ) {
			return null;
		}

		if ( empty( $options ) ) {
			return $value;
		}

		//make sure it is an array
		$value = (array) $value;
		$values = [];
		foreach ( $value as $key ) {
			if ( isset( $options[ $key ] ) ) {
				$values[] = $options[ $key ];
			}
		}
		if ( count( $values ) === 1 ) {
			return $values[0];
		} else {
			return $values;
		}
	}
}
