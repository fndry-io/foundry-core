<?php

namespace Foundry\Core\Models\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Trait Sluggable
 *
 * Create a unique slug for a given model
 *
 * @package Foundry\Traits
 *
 * @author Medard Ilunga
 *
 */
trait Sluggable {

	/**
	 * Laravel Model Boot function
	 */
	protected static function bootSluggable() {
		static::creating( function ( $model ) {
			/**@var $model Sluggable */
			if (empty($model->{$model->getSluggableColumn()})) {
				$model->{$model->getSluggableColumn()} = $model->createSlug( $model->{$model->getSluggableSourceColumn()} );
			}
		} );
	}

	/**
	 * Create a new slug
	 *
	 * @param string $text
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function createSlug( string $text ) {
		//Create slug
		$slug  = Str::slug( $text );
		$column = $this->getSluggableColumn();
		// Get any that could possibly be related.
		$allSlugs = $this->getRelatedSlugs( $slug );
		// If we haven't used it before then we are all good.
		/**@var $allSlugs Collection */
		if ( ! $allSlugs->contains( $column, $slug ) ) {
			return $slug;
		}
		// Just append numbers until we find one not used.
		for ( $i = 1; $i <= 1000; $i ++ ) {
			$newSlug = $slug . '-' . $i;
			if ( ! $allSlugs->contains( $column, $newSlug ) ) {
				return $newSlug;
			}
		}

		throw new \Exception( 'Can not create a unique slug' );
	}

	/**
	 * Get models with given slug
	 *
	 * @param $slug
	 *
	 * @return mixed
	 */
	private function getRelatedSlugs( $slug ) {
		if (method_exists($this, 'trashed')) {
			$query = static::withTrashed();
		} else {
			$query = static::query();
		}
		$query->select( $this->getQualifiedSluggableColumn(), $this->getQualifiedSluggableSourceColumn() )->where( $this->getQualifiedSluggableColumn(), 'like', $slug . '%' );

		if ($namespaced = $this->getSluggableNamespaceColumn()) {
		    $query->where($this->getQualifiedSluggableNamespaceColumn(), '=', $this->$namespaced);
        }

		//ensure not the same record
		if ( $key = $this->getKey() ) {
			$query->where( $this->getQualifiedKeyName(), '!=', $key );
		}

		return $query->get();
	}

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getSluggableColumn()
    {
        return defined('static::SLUGGABLE_COLUMN') ? static::SLUGGABLE_COLUMN : 'slug';
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getSluggableNamespaceColumn()
    {
        return defined('static::SLUGGABLE_NAMESPACE_COLUMN') ? static::SLUGGABLE_NAMESPACE_COLUMN : null;
    }

    /**
     * Get the fully qualified "namespaced at" column.
     *
     * @return string
     */
    public function getQualifiedSluggableColumn()
    {
        return $this->qualifyColumn($this->getSluggableColumn());
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getSluggableSourceColumn()
    {
        return defined('static::SLUGGABLE_SOURCE') ? static::SLUGGABLE_SOURCE : 'name';
    }

    /**
     * Get the fully qualified "namespaced at" column.
     *
     * @return string
     */
    public function getQualifiedSluggableSourceColumn()
    {
        return $this->qualifyColumn($this->getSluggableSourceColumn());
    }


    /**
     * Get the fully qualified "namespaced at" column.
     *
     * @return string
     */
    public function getQualifiedSluggableNamespaceColumn()
    {
        return $this->qualifyColumn($this->getSluggableNamespaceColumn());
    }

}
