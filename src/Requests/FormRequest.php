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
	 * FormRequest constructor.
	 *
	 * @param array $query
	 * @param array $request
	 * @param array $attributes
	 * @param array $cookies
	 * @param array $files
	 * @param array $server
	 * @param null $content
	 */
	public function __construct( array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null ) {
		parent::__construct( $query, $request, $attributes, $cookies, $files, $server, $content );
		$this->input = $this->makeInput($this->all());
		if ($id = $this->input('_id')) {
			$this->getEntity($id);
		}
	}

	/**
	 * The name of the Request for registering it in the FormRequest Container
	 *
	 * @return String
	 */
	abstract static function name() : String;

	/**
	 * Handle the request
	 *
	 * @return Response
	 */
	abstract public function handle() : Response;

	/**
	 * The input class for this form request
	 *
	 * @return string
	 */
	abstract static function getInputClass(): string;

	/**
	 * Get the Entity for the request
	 *
	 * @param mixed $id The ID of the entity to fetch
	 *
	 * @return null|object|Entity|EntityInterface
	 */
	abstract public function getEntity($id);

	/**
	 * The rules for this form request
	 *
	 * This is derived off of the input class rules method
	 *
	 * @return array
	 */
	public function rules()
	{
		return $this->input->rules();
	}

	/**
	 * Build a form object for this form request
	 *
	 * @return FormType
	 */
	public function form() : FormType
	{
		if ($this->entity) {
			$this->input->setEntity($this->entity);
		}

		$form = new FormType( static::name() );
		$params = ['_request' => static::name() ];
		if ($this->entity) {
			$params['_id'] = $this->entity->getId();
		}

		$form
			->setEntity($this->entity)
			->attachInputCollection($this->input)
			->setAction( route('system.request.handle', $params ) )
		;
		$form
			->setRequest( $this )
			->setValues( $this->only( $this->input->keys() ) )
		;

		return $form;
	}

	/**
	 * Make the input class for the request
	 *
	 * @param $inputs
	 *
	 * @return mixed
	 */
	public function makeInput($inputs)
	{
		$class = static::getInputClass();
		return new $class($inputs);
	}

	/**
	 * Get the input class for the request
	 *
	 * @return Inputs|mixed
	 */
	public function getInput()
	{
		return $this->input;
	}
}
