<?php

namespace Foundry\Core\Seeders;

use Foundry\Core\Repositories\SettingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

/**
 * Class SettingSeeder
 *
 * Extends this class if the Plugin has a Setting model and requires seeding
 *
 * @package Foundry\Seeders
 */
trait SettingSeeder{

    /**
	 * @return array
	 */
	protected abstract function settings(): array;

	protected abstract function model(): string;

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function seed() {
		$table = SettingRepository::getTable();

		if ( ! Schema::hasTable( $table ) ) {
			Artisan::call( 'migrate' );
		}else{
		    DB::table($table)->truncate();
        }

		$illegal = 0;

		$class = $this->model();

		foreach ( $this->settings() as $key => $setting ) {

			$domain_name = explode( '.', $key );

			if ( sizeof( $domain_name ) === 2 ) {

				//Check if setting with given domain and name exists
				$model = $class::where( 'domain', $domain_name[0] )
				               ->where( 'name', $domain_name[1] )->first();

				//If no model exists, create new one
				if ( ! $model ) {
					$model = new $class();
				}

				$model->domain = $domain_name[0];
				$model->name   = $domain_name[1];
				$model->model  = $this->model();
				$type          = isset( $setting['type'] ) ? $setting['type'] : 'string';

				$model->type    = $type;
				$model->default = isset( $setting['default'] ) ? $setting['default'] : $this->getDefaultBasedOnType( $type );

				$model->save();

			} else {
				$illegal += 1;
			}

		}


		if ( $illegal > 0 ) {
			dd( 'There was/were ' . $illegal . ' setting(s) with illegal names' );
		}

	}

	/**
	 * @param $type
	 *
	 * @return array|int|string
	 */
	private function getDefaultBasedOnType( $type ) {
		switch ( $type ) {
			case 'int':
			case 'integer':
				return 1;
				break;
			case 'string':
				return '';
				break;
			case 'array':
				return [];
				break;
			case 'bool':
			case 'boolean':
				return 0;
				break;
		}

		return '';
	}
}
