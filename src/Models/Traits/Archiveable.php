<?php

namespace Foundry\Core\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Archiveable
 *
 * @package Foundry\Core\Traits
 */
trait Archiveable
{

	/**
	 * Initialize the archivable trait for an instance.
	 *
	 * @return void
	 */
	public function initializeArchiveable()
	{
		$this->dates[] = $this->getArchivedAtColumn();
	}

	public function scopeOnlyArchived(Builder $query)
	{
		return $query->whereNotNull($this->getQualifiedArchivedAtColumn());
	}

    public function scopeWithoutArchived(Builder $query)
	{
		return $query->whereNull($this->getQualifiedArchivedAtColumn());
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
	public static function unArchiving($callback)
	{
		static::registerModelEvent('unArchiving', $callback);
	}

	/**
	 * Register a restored model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function unArchived($callback)
	{
		static::registerModelEvent('unArchived', $callback);
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
