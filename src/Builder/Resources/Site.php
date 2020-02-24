<?php

namespace Foundry\Core\Builder\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Site
 *
 * @package Foundry\Core\Builder\Resources
 */
class Site extends JsonResource {

    public function toArray( $request ) {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'updated_at' => $this->updated_at
        ];
    }
}
