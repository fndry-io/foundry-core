<?php

namespace Foundry\Core\Services;

use Foundry\Core\Requests\FormRequest;
use Foundry\Core\Requests\Response;
use Foundry\Core\Services\Service as FoundryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic Service
 *
 * @package Plugins\Foundry\System\Services
 */
abstract class GenericService extends FoundryService {

	/**
	 * Returns the model to use for this service
	 *
	 * @return Model
	 */
	abstract static public function modelClass(): string;


	/**
	 * Returns the translated resource name for use in messages
	 *
	 * @return Model
	 */
	abstract static public function resourceName(): string;


	/**
	 * {@inheritdoc}
	 */
	static function model( $id ) {
		return static::modelClass()::query()->where( 'id', $id )->first();
	}

	/**
	 * Get the stores
	 *
	 * @param \Closure $closure A closure which will be given the Builder as the first parameter
	 *
	 * @return Response
	 */
	static public function find( \Closure $closure = null ): Response {
		$query = static::modelClass()::query();

		if ( $closure ) {
			$closure( $query );
		}

		return Response::success( $query->paginate( setting( 'system.default_pagination', 20 ) ) );
	}

	/**
	 * Return a query object
	 *
	 * @return Builder
	 */
	static public function query(): Builder {
		return static::modelClass()::query();
	}

	/**
	 * Get the specified resource or all.
	 *
	 * @param $id
	 *
	 * @return Response
	 */
	static function get( $id ) {
		if ( $id && $store = static::model( $id ) ) {
			return Response::success( $store );
		} else {
			return Response::error( __( "Requested :resource was not found", [ 'resource' => static::resourceName() ] ), 404 );
		}
	}


	/**
	 * Create or Update resource
	 *
	 * @param FormRequest $form
	 * @param $id
	 *
	 * @return Response
	 */
	static function upsert( FormRequest $form, $id ) {
		$class = static::modelClass();
		$model = new $class();

		if ( $id && ! $model = static::modelClass()::query()->where( 'id', $id )->first() ) {
			return Response::error( __( "Requested :resource was not found", [ 'resource' => static::resourceName() ] ), 404 );
		}

		$response = $form->validate();

		if ( $response->isSuccess() ) {
			$inputs = $response->getData();
			$model->fill( $inputs );
		} else {
			return $response;
		}

		if ( $model->save() ) {
			return Response::success( $model );
		} else {
			return Response::error( __( "Unable to save :resource", [ 'resource' => static::resourceName() ] ), 500 );
		}
	}


	/**
	 * Destroy an Store
	 *
	 * @param Model|int $model The role ID or model
	 *
	 * @return Response
	 */
	static public function destroy( $model ): Response {
		if ( ! is_object( $model ) ) {
			$model = static::model( $model );
		}

		if ( ! $model ) {
			return Response::error( __( "Requested :resource was not found", [ 'resource' => static::resourceName() ] ), 404 );
		}

		try {
			$result = $model->delete();
		} catch ( \Exception $e ) {
			$result = false;
		} finally {
			if ( $result ) {
				return Response::success( [], __( ":resource deleted", [ 'resource' => static::resourceName() ] ) );
			} else {
				return Response::error( __( "Unable to delete :resource", [ 'resource' => static::resourceName() ] ), 500 );
			}
		}
	}

}
