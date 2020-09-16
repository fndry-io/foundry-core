<?php

namespace Foundry\Core\Inputs\Types;

use Parsedown;

/**
 * Class ContentType
 *
 * @package Foundry\Requests\Types
 */
class MarkdownType extends ContentType {

	public function setContent(string $content)
    {
        $this->setAttribute('content', (new Parsedown())->text($content));
        return $this;
    }

}
