<?php

namespace Foundry\Core\Models\Traits;

use Foundry\Core\Models\Fields\FieldType;
use Foundry\Core\Models\InputCollection;
use Foundry\Core\Requests\Types\Contracts\Inputable;

/**
 * Trait HasInputCollection
 *
 * Allows us to generate a list of Input Types for a Model
 *
 * @package Foundry\Models\Traits
 */
trait HasFieldInputCollection {

	public function inputs( array $names = null, $collection = null ): InputCollection {
		if ( $collection ) {
			$collection = $collection . '.';
		}
		if ($names === null) {
			$names = array_keys(static::$inputs);
		}
		$inputs = new InputCollection();
		foreach ( $names as $key => $value ) {
			//have we been given a custom InputType
			if ( is_object( $value ) ) {
				$input = $value;
				//have we been given a custom input name
				if ( is_string( $key ) ) {
					$key = $collection . $key;
				} else {
					$key = $collection . $input->getName();
				}

				$input->setName( $key );
				$inputs->offsetSet( $key, $input );

			} //have we been given a default input, meaning the key to find on the model::$inputs where we will get the InputType class
			else if ( is_string( $value ) && isset( static::$inputs[ $value ] ) ) {
				/**
				 * @var FieldType $field
				 */
				$field = static::$inputs[ $value ];
				$input = $field::input( $this );
				//set to the given name from the static::inputs
				$key   = $collection . $value;

				$input->setName( $key );
				$inputs->offsetSet( $key, $input );
			}
		}

		return $inputs;
	}

	public function input($name) : Inputable
	{
		if (!isset(self::$inputs[$name])) {
			throw new \Exception(sprintf('Input %s not found on Model %s', $name, static::class));
		}

		/**
		 * @var FieldType $field
		 */
		$field = self::$inputs[ $name ];
		$input = $field::input( $this );
		return $input->setModel($this);
	}
}