<?php

namespace Foundry\Core\View\Components\Traits;

use Foundry\Core\Requests\FormRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HasReadFormRequest {

	/**
	 * Bind data to the view.
	 *
	 * @param string $class The FormQuest class to use
	 * @param Request $request The current request
	 * @param Model $model The model to read
	 * @param string $model_key The key to set for the loaded model in the view
	 *
	 * @return void
	 */
	public function handleRequest( $class, $request, $model, $model_key = 'data' ) {
		/**
		 * @var FormRequest $class
		 */
		$response = $class::handleRequest( $request, $model );

		if ( $response->isSuccess() ) {
			$form = $class::view( $request, $response->getData() );
			$this->set( [
				$model_key => $response->getData(),
				'view'     => $form
			] );
		} else {
			abort( $response->getCode(), $response->getMessage() );
		}
	}

}