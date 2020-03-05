<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;
use Illuminate\Support\Str;

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
		return $this->getAttribute('id');
	}

	/**
	 * @param string|null $id
	 *
	 * @return $this
	 */
	public function setId( $id = null ) {
		if ( $id == null ) {
			if ( method_exists( $this, 'getName' ) ) {
				$id = uniqid(ucfirst(Str::camel( Str::slug( str_replace('.', '_', $this->getName()) . '_' . $this->getType() ) )));
			} else {
				$id = uniqid( 'Id' );
			}
		}
		$this->setAttribute('id', $id);

		return $this;
	}

}
