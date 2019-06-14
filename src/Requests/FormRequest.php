<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Entities\Contracts\EntityInterface;
use Foundry\Core\Inputs\Inputs;
use Foundry\Core\Inputs\Types\FormType;
use Foundry\System\Entities\Entity;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

/**
 * Class FormRequest
 *
 * @package Foundry\Requests
 */
abstract class FormRequest extends LaravelFormRequest {

	/**
	 * @var Inputs
	 */
	protected $input;

	/**
	 * @var null|EntityInterface
	 */
	protected $entity = null;

	/**
	 * @param array $query
	 * @param array $request
	 * @param array $attributes
	 * @param array $cookies
	 * @param array $files
	 * @param array $server
	 * @param null $content
	 */
	public function initialize( array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null ) {
		parent::initialize( $query, $request, $attributes, $cookies, $files, $server, $content );
		if ( $input = $this->makeInput( $this->all() ) ) {
			$this->setInput( $input );
		}
		if ( $id = $this->input( '_id' ) ) {
			$this->getEntity( $id );
		}
	}

	/**
	 * The name of the Request for registering it in the FormRequest Container
	 *
	 * @return String
	 */
	abstract static function name(): String;

	/**
	 * Handle the request
	 *
	 * @return Response
	 */
	abstract public function handle(): Response;

	/**
	 * The input class for this form request
	 *
	 * @return string|null
	 */
	abstract static function getInputClass();

	/**
	 * Get the Entity for the request
	 *
	 * @param mixed $id The ID of the entity to fetch
	 *
	 * @return null|object|Entity|EntityInterface
	 */
	abstract public function getEntity( $id );

	/**
	 * The rules for this form request
	 *
	 * This is derived off of the input class rules method
	 *
	 * @return array
	 */
	public function rules() {
		return $this->input->rules();
	}

	/**
	 * Build a form object for this form request
	 *
	 * @return FormType
	 */
	public function form(): FormType {

		$form   = new FormType( static::name() );
		$params = [ '_request' => static::name() ];
		if ( $this->entity ) {
			$params['_id'] = $this->entity->getId();
		}

		if ( $this->entity && $this->input ) {
			$this->input->setEntity( $this->entity );
		}
		$form->setEntity( $this->entity );
		if ( $this->input ) {
			$form->attachInputCollection( $this->input->types() );
			$form->setValues( $this->only( $this->input->keys() ) );
		}
		$form->setAction( route( 'system.request.handle', $params ) );
		$form->setRequest( $this );

		return $form;
	}

	/**
	 * Make the input class for the request
	 *
	 * @param $inputs
	 *
	 * @return mixed
	 */
	public function makeInput( $inputs ) {
		if ( $class = static::getInputClass() ) {
			return new $class( $inputs );
		} else {
			return null;
		}
	}

	/**
	 * Get the input class for the request
	 *
	 * @return Inputs|null|mixed
	 */
	public function getInput() {
		return $this->input;
	}

	/**
	 * @param Inputs $input
	 */
	public function setInput( Inputs $input ): void {
		$this->input = $input;
	}
}
