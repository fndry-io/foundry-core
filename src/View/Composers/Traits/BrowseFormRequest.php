<?php

namespace Foundry\Core\View\Composers\Traits;

use Foundry\Core\Requests\Contracts\ColumnInterface;
use Foundry\Core\Requests\FormRequest;
use Foundry\Core\Requests\Types\DocType;
use Foundry\Core\Requests\Types\ResetButtonType;
use Foundry\Core\Requests\Types\SubmitButtonType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

trait BrowseFormRequest {

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
	 *
	 * @return void
	 */
	public function handle( string $class, Request $request, View &$view ) {
		/**
		 * @var FormRequest $class
		 */
		$response = $class::handleRequest( $request );

		if ( $response->isSuccess() ) {
			$this->view( $class, $request, $view, $response );
		} else {
			abort( $response->getCode(), $response->getMessage() );
		}
	}

	/**
	 * @param string $class
	 * @param Request $request
	 * @param View $view
	 * @param $response
	 */
	protected function view( string $class, Request $request, View &$view, $response = null ): void {
		/**
		 * @var FormType $form
		 */
		$form = $class::form( $request, null );
		$doctype = null;
		if ($inputs = $form->getInputs()) {
			$form
				->setMethod( 'GET' )
				->setButtons(
					( new SubmitButtonType( __( 'Filter' ), $form->getAction() ) )
					//,( new ResetButtonType( __( 'Reset' ), $form->getAction() ) )
				);

			if ( $response && ! $response->isSuccess() ) {
				$form->setErrors( $response->getError() );
			}
			$form->addChildren(...array_values($inputs));
			$doctype = DocType::withChildren( $form );
		}

		$view->with( [
			'data' => ($response) ? $response->getData() : null,
			'form' => $doctype
		] );
		if ( method_exists( $class, 'columns' ) ) {
			$view->with( 'columns', $class::columns() );
		}
	}

}