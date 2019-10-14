<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Entities\Contracts\IsEntity;
use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Contracts\InputInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

trait HasReference
{

    /**
     * @return IsEntity|Builder|Builder[]|Collection|Model|null
     */
	public function getReference()
	{
		if (($type = $this->input('reference_type')) && ($id = $this->input('reference_id'))) {
			if ($reference = $this->findReference($type, $id)) {
				return $reference;
			} else {
				throw new NotFoundHttpException(__('Associated object not found'));
			}
		} else {
			throw new UnprocessableEntityHttpException(__('Associated object not provided'));
		}
	}

    /**
     * @param string $type The Reference type
     * @param int $id The Reference id
     *
     * @return null
     */
	public function findReference($type, $id)
    {
        if (class_exists($type) && is_a($type, Model::class, true) && $reference = $type::query()->withoutGlobalScopes()->find($id)) {
            return $reference;
        }
        return null;
    }

}
