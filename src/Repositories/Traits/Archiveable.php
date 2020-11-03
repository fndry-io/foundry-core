<?php

namespace Foundry\Core\Repositories\Traits;

use Foundry\Core\Models\Contracts\IsArchiveable;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait Archiveable
{

	/**
	 * Archive an entity in the database
	 *
	 * @param int|Model|IsArchiveable $id
	 *
	 * @return boolean
	 */
	public function archive($id)
	{
		if ($id instanceof Model) {
			$model = $id;
		} else {
			if (!$model = $this->find($id)) {
				throw new NotFoundHttpException();
			}
		}
		$result = $model->archive();
		if ($result) {
		    $this->dispatch('archived', $model);
        }
		return $result;
	}

	/**
	 * @param int|Model|IsArchiveable $id
	 *
	 * @return boolean
	 */
	public function unArchive($id)
	{
		if ($id instanceof Model) {
			$model = $id;
		} else {
			if (!$model = $this->find($id)) {
				throw new NotFoundHttpException();
			}
		}
        $result = $model->unArchive();
        if ($result) {
            $this->dispatch('unArchived', $model);
        }
        return $result;
    }
}
