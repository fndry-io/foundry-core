<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasConfig;

class SimpleHtmlType extends HtmlType
{
    use HasConfig;

    /**
     * @var array The default toolbar options
     */
    protected $toolbar = [
        ['bold', 'italic', 'underline', 'strike'],
    ];

}
