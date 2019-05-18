<?php

namespace Foundry\Core\View\Components\Traits;

use Foundry\Core\Requests\FormRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

trait HasFormFormRequest {

	/**
	 * Bind data to the view.
	 *
	 * @param string $class The FormQuest class to use
	 * @param Request $request The current request
	 * @param View $view The view to update
	 *
	 * @return void
	 */
	public function handleRequest( string $class, Request $request, View &$view, $model = null ) {
		/**
		 * @var FormRequest $class
		 */
		$form = $class::form( $request, $model );
		$this->set( [
			'form' => $form
		] );
	}

}