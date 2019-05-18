<?php

namespace Foundry\Core\View\Components;

use Foundry\Core\Exceptions\ViewComponentException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\Finder\Exception\AccessDeniedException;

/**
 * ViewComponentHandler
 *
 * This class helps us register and handle view component requests
 *
 * View Component are a combination between Controllers and View Composer
 *
 * @package Foundry\View\Components
 */
class ViewComponentHandler implements \Foundry\Contracts\ViewComponentHandler {

	protected $components;

	protected $request;

	public function __construct(Request $request) {
		$this->request = $request;
	}

	/**
	 * Register a view component class
	 *
	 * @param string $class The class name
	 * @param string $key
	 *
	 * @return void
	 * @throws ViewComponentException
	 */
	public function register( $class, $key = null ) {
		if ( is_array( $class ) ) {
			foreach ( $class as $_class ) {
				$this->registerComponent( $_class, forward_static_call( [ $_class, 'name' ] ) );
			}
		} else {
			$this->registerComponent( $class, $key );
		}
	}

	/**
	 * Registers the component with the given key
	 *
	 * @param $class
	 * @param $key
	 *
	 * @throws ViewComponentException
	 */
	protected function registerComponent( $class, $key ) {

		if ( isset( $this->components[ $key ] ) ) {
			throw new ViewComponentException( sprintf( "View Component key %s already used", $key ) );
		}
		$this->components[ $key ] = $class;
	}

	/**
	 * Handle the requested form with the request
	 *
	 * @param $key
	 * @param $params
	 *
	 * @return View
	 * @throws ViewComponentException
	 */
	public function handle( $key, $params ): View {
		$component = $this->getComponent( $key );
		/**
		 * @var ViewComponent $component
		 */
		$component = new $component($params, $this->request);
		$component->init();
		if (!$component->permission()) {
			throw new AccessDeniedException(__('You do not have permission to view this'));
		}
		return $component->render();
	}

	/**
	 * Get the form request class
	 *
	 * @param $key
	 *
	 * @return string
	 * @throws ViewComponentException
	 */
	protected function getComponent( $key ): string {
		if ( ! isset( $this->components[ $key ] ) ) {
			throw new ViewComponentException( sprintf( "View Component %s not registered", $key ) );
		}

		return $this->components[ $key ];
	}

	/**
	 * List all the registered forms
	 *
	 * @return array
	 */
	public function getList()
	{
		return array_keys($this->components);
	}

}