<?php

namespace Foundry\Core\Requests\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

trait IsBrowseRequest {

	public function makeBrowseResource(Paginator $paginator, $page, $limit, $resource_class = null)
	{
		if ($resource_class) {
			$results = $resource_class::collection(Collection::make($paginator->getIterator()));
		} else {
			$results = [];
			foreach ($paginator->getIterator() as $item) {
				$results[] = $item;
			}
		}
		$lengthAwarePaginator = new \Illuminate\Pagination\LengthAwarePaginator( $results, count( $paginator ), $limit, $page );
		$lengthAwarePaginator->setPath($this->fullUrl());
		return $lengthAwarePaginator;
	}

}