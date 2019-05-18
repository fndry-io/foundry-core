<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasId {

	/**
	 * Input id
	 *
	 * @var string $id
	 */
	protected $id;

	/**
	 * @return string
	 */
	public function getId(): string {
		return $this->id;
	}

	/**
	 * @param string|null $id
	 *
	 * @return InputType
	 */
	public function setId( $id = null ) {
		if ( $id == null ) {
			if ( method_exists( $this, 'getName' ) ) {
				$this->id = ucfirst(camel_case( str_slug( str_replace('.', '_', $this->getName()) . '_' . $this->getType() ) ));
			} else {
				$this->id = uniqid( 'Id' );
			}
		} else {
			$this->id = $id;
		}

		return $this;
	}

}