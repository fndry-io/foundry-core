<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Models\InputCollection;
use Foundry\Core\Requests\Response;
use Foundry\Core\Requests\Types\DocType;
use Foundry\Core\Requests\Types\FormType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait IsDeleteRequest {

	static public function rules( Model $model = null ): array {
		return [];
	}

	static function inputCollection( $model = null ): InputCollection {
		return new InputCollection();
	}

	static function fields(): array {
		return [];
	}

	/**
	 *
	 * @param Request|null $request
	 * @param Model|null $model
	 *
	 * @return FormType
	 */
	static function form( Request $request = null, $model = null ): FormType {
		$form = new FormType( static::name() );

		return $form;
	}

	public static function view( Request $request = null, $model = null ): DocType {
		$form = static::form( $request, $model );
		$form
			->setRequest( $request )
			->setValues( $request->only( static::fields() ) );

		return ( new DocType() )->addChildren( $form );
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle(): Response {
		$response = $this->validate();
		if ( $response->isSuccess() ) {
			if ( $model = $this->getModel() ) {
				return static::service()::destroy( $model );
			} else {
				return Response::error( __( 'Record not found' ), 404 );
			}
		} else {
			return $response;
		}
	}
}