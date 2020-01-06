<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Illuminate\Http\Request;

trait ViewableInput
{
    /**
     * Build a form object for the given request
     *
     * If an entity is supplied through the route {_entity} it will be set into the form here
     *
     * All inputs from the request are also passed into the form using the the keys (names) of the inputs in the Input Class
     *
     * @param Request $request
     * @param array $params
     * @return FormType
     */
    public function form(Request $request, array $params = []): FormType {

        $form = new FormType( uniqid('form_') );

        if ($request instanceof EntityRequestInterface && ($entity = $request->getEntity())) {
            $params['_entity'] = $entity->getKey();
            $form->setEntity( $entity );
        }

        $form->attachInputCollection( $this->getTypes() );
        $inputs = $this->only($this->keys());
        $this->cast($inputs);
        $form->setValues( $inputs );

        $form->setAction( $request->fullUrl() );
        $form->setParams( $params );
        $form->setRequest( $request );

        return $form;
    }
}
