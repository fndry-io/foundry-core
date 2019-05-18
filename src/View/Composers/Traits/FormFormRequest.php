<?php

namespace Foundry\Core\View\Composers\Traits;

use Foundry\Core\Requests\FormRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

trait FormFormRequest {

	/**
	 * Bind data to the view.
	 *
	 * @param string $class The FormQuest class to use
	 * @param Request $request The current request
	 * @param View $view The view to update
	 *
	 * @return void
	 */
	public function handle( string $class, Request $request, View &$view, $model = null ) {
		/**
		 * @var FormRequest $class
		 */
		$form = $class::form( $request, $model );
		$view->with( [
			'form' => $form
		] );
	}

}