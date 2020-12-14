<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Inputs;
use Foundry\Core\Inputs\Types\Traits\HasLabel;

/**
 * Class FormInputType
 *
 * Allows us to display a form to the user for completion
 *
 * @package Foundry\Core\Inputs\Types
 */
class FormInputType extends InputType {

	use HasLabel;

	protected $cast = 'array';

	protected Inputs $form;

    /**
     * FormInputType constructor.
     * @param Inputs $inputs
     * @param null $name
     * @param null $label
     * @param bool $required
     */
	public function __construct(Inputs $inputs, $name = null, $label = null, $required = false) {
		parent::__construct($name, $label, $required);
		$this->setType('subform');
		$this->setForm($inputs);
	}

    /**
     * Sets the the collection/form schema to use for rendering this form in the front end
     *
     * @param Inputs $inputs
     * @return FormInputType
     */
	public function setForm(Inputs $inputs): FormInputType
    {
        $this->form = $inputs;
        return $this;
    }

    public function getForm(): Inputs
    {
        return $this->form;
    }

    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();
        if (!empty($this->inputs)) {
            $json['schema'] = $this->form->view(app('request'));
        }
        return $json;
    }


}
