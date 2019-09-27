<?php

namespace Foundry\Core\Models\Traits;

use Carbon\Carbon;
use Foundry\Core\Models\ArchivedScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Archiveable
 *
 * @package Foundry\Core\Traits
 */
trait Archiveable
{

	/**
	 * Boot the soft deleting trait for a model.
	 *
	 * @return void
	 */
	public static function bootArchiveable()
	{
		static::addGlobalScope(new ArchivedScope());
	}

	/**
	 * Initialize the soft deleting trait for an instance.
	 *
	 * @return void
	 */
	public function initializeSoftDeletes()
	{
		$this->dates[] = $this->getArchivedAtColumn();
	}

	public function scopeArchived(Builder $query)
	{
		return $query->whereNotNull($this->getArchivedAtColumn());
	}

	public function scopeUnarchived(Builder $query)
	{
		return $query->whereNotNull($this->getArchivedAtColumn());
	}

	public function setArchivedAt( $archived_at ): void {
		$this->archived_at = $archived_at;
	}

	public function getArchivedAt() {
		return $this->archived_at;
	}

	public function isArchived()
	{
		return ! is_null($this->{$this->getArchivedAtColumn()});
	}

	/**
	 * Archive am archived model instance.
	 *
	 * @return bool|null
	 */
	public function archive()
	{
		// If the restoring event does not return false, we will proceed with this
		// restore operation. Otherwise, we bail out so the developer will stop
		// the restore totally. We will clear the deleted timestamp and save.
		if ($this->fireModelEvent('archiving') === false) {
			return false;
		}

		$this->{$this->getArchivedAtColumn()} = new Carbon();

		// Once we have saved the model, we will fire the "restored" event so this
		// developer will do anything they need to after a restore operation is
		// totally finished. Then we will return the result of the save call.
		$this->exists = true;

		$result = $this->save();

		$this->fireModelEvent('archived', false);

		return $result;
	}


	/**
	 * Unarchive am archived model instance.
	 *
	 * @return bool|null
	 */
	public function unArchive()
	{
		// If the restoring event does not return false, we will proceed with this
		// restore operation. Otherwise, we bail out so the developer will stop
		// the restore totally. We will clear the deleted timestamp and save.
		if ($this->fireModelEvent('unArchiving') === false) {
			return false;
		}

		$this->{$this->getArchivedAtColumn()} = null;

		// Once we have saved the model, we will fire the "restored" event so this
		// developer will do anything they need to after a restore operation is
		// totally finished. Then we will return the result of the save call.
		$this->exists = true;

		$result = $this->save();

		$this->fireModelEvent('unArchived', false);

		return $result;
	}

	/**
	 * Register a restoring model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function unarchiving($callback)
	{
		static::registerModelEvent('unarchiving', $callback);
	}

	/**
	 * Register a restored model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function unarchived($callback)
	{
		static::registerModelEvent('unarchived', $callback);
	}

	/**
	 * Register a restoring model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function archiving($callback)
	{
		static::registerModelEvent('archiving', $callback);
	}

	/**
	 * Register a restored model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function archived($callback)
	{
		static::registerModelEvent('archived', $callback);
	}

	/**
	 * Get the name of the "deleted at" column.
	 *
	 * @return string
	 */
	public function getArchivedAtColumn()
	{
		return defined('static::ARCHIVED_AT') ? static::ARCHIVED_AT : 'archived_at';
	}

	/**
	 * Get the fully qualified "archived at" column.
	 *
	 * @return string
	 */
	public function getQualifiedArchivedAtColumn()
	{
		return $this->qualifyColumn($this->getArchivedAtColumn());
	}

}
