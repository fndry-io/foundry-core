<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Contracts\InputInterface;
use Foundry\System\Models\Node;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

trait HasNode
{

    /**
     * @var Node $node
     */
    protected $node;

	/**
	 * @return Node
	 */
	public function getNode()
	{
	    if ($this->node) {
	        return $this->node;
        } elseif ($id = $this->input('node')) {
			if ($this->node = Node::query()->find($id)) {
				return $this->node;
			}
		}
        return null;
	}

}
