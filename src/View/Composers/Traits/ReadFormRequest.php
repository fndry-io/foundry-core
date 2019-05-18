<?php

namespace Foundry\Core\View\Composers\Traits;

use Foundry\Core\Requests\FormRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait ReadFormRequest {

	protected $request;

	/**
	 * BrowseFormRequest constructor.
	 *
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Bind data to the view.
	 *
	 * @param string $class The FormQuest class to use
	 * @param Request $request The current request
	 * @param View $view The view to update
	 * @param Model $model The model to read
	 * @param string $model_key The key to set for the loaded model in the view
	 *
	 * @return void
	 */
	public function handle( $class, $request, View &$view, $model, $model_key = 'data' ) {
		/**
		 * @var FormRequest $class
		 */
		$response = $class::handleRequest( $request, $model );

		if ( $response->isSuccess() ) {
			$form = $class::view( $request, $response->getData() );
			$view->with( [
				$model_key => $response->getData(),
				'view'     => $form
			] );
		} else {
			abort( $response->getCode(), $response->getMessage() );
		}
	}

}