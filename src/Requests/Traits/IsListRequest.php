<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Requests\Response;
use Illuminate\Database\Eloquent\Model;

trait IsListRequest {

	static public function rules( Model $model = null ): array {
		return [];
	}

	static function fields(): array {
		return [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle(): Response {
		$response = $this->validate();
		if ( $response->isSuccess() ) {
			if ( $model = $this->getModel() ) {
				return Response::success( $model );
			} else {
				return Response::error( __( 'Record not found' ), 404 );
			}
		} else {
			return $response;
		}
	}

}