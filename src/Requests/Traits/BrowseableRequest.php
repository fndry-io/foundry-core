<?php

namespace Foundry\Core\Requests\Traits;

trait BrowseableRequest {

    /**
     * Extracts meta data from the request for use with the browse
     *
     * @param $page
     * @param $limit
     * @param $sortBy
     * @param $sortDesc
     * @return array
     */
    public function getBrowseMeta($page, $limit, $sortBy, $sortDesc): array
    {
        $page = $this->input('page', $page);
        $limit = $this->input('limit', $limit);
        $sortBy = $this->input('sortBy', $sortBy);
        $sortDesc = $this->input('sortDesc', $sortDesc) === 'true';
        return array($page, $limit, $sortBy, $sortDesc);
    }

}
