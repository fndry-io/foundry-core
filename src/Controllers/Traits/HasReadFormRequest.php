<?php

namespace Foundry\Core\Controllers\Traits;

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
	 * @param Model|null $model The model to read
	 * @param string $model_key The key to set for the loaded model in the view
	 *
	 * @return array
	 */
	public function handleReadRequest( $class, $request, $model = null, $model_key = 'data' ) : array
	{
		/**
		 * @var FormRequest $class
		 */
		$response = $class::handleRequest( $request, $model );

		if ( $response->isSuccess() ) {
			$form = $class::view( $request, $response->getData() );
			return [
				$model_key => $response->getData(),
				'view'     => $form
			];
		} else {
			abort( $response->getCode(), $response->getMessage() );
		}
	}

}