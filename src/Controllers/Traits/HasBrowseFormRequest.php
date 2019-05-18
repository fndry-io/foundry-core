<?php

namespace Foundry\Core\Controllers\Traits;

use Foundry\Core\Requests\FormRequest;
use Foundry\Core\Requests\Types\DocType;
use Foundry\Core\Requests\Types\FormType;
use Foundry\Core\Requests\Types\SubmitButtonType;
use Illuminate\Http\Request;

trait HasBrowseFormRequest {

	/**
	 * Bind data to the view.
	 *
	 * @param string $class The FormQuest class to use
	 * @param Request $request The current request
	 *
	 * @return array
	 */
	public function handleBrowseRequest( string $class, Request $request ) : array
	{
		/**
		 * @var FormRequest $class
		 */
		$response = $class::handleRequest( $request );

		if ( $response->isSuccess() ) {
			return $this->handleBrowseResponse( $class, $request, $response );
		} else {
			abort( $response->getCode(), $response->getMessage() );
		}
	}

	/**
	 * @param string $class
	 * @param Request $request
	 * @param $response
	 *
	 * @return array
	 */
	protected function handleBrowseResponse( string $class, Request $request, $response = null ): array {
		/**
		 * @var FormType $form
		 */
		$form = $class::form( $request, null );
		$doctype = null;

		$data = [];

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

		$data['form'] = $doctype;
		$data['data'] = ($response) ? $response->getData() : null;

		if ( method_exists( $class, 'columns' ) ) {
			$data['columns'] = $class::columns();
		}

		return $data;
	}

}