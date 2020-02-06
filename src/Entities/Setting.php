<?php

namespace Foundry\Core\Entities;

use Foundry\Core\Entities\Traits\Identifiable;
use Foundry\Core\Entities\Traits\Timestampable;

class Setting extends Entity {

    use Timestampable;
    use Identifiable;

    /**
     * @var array The fillable values
     */
    protected $fillable = [
        'name'
    ];

    protected $visible = [
        'id',
        'name'
    ];

    protected $name;


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
