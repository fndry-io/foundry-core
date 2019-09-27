<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\FormType;

trait HasForm
{
	/**
	 * @var FormType|null
	 */
	protected $form;

	public function getForm() : ?FormType
	{
		return $this->form;
	}

	public function setForm(FormType &$form)
	{
		$this->form = $form;
	}

	public function getEntity()
	{
		return $this->form->getEntity();
	}

	public function hasEntity()
	{
		return $this->form->hasEntity();
	}
}