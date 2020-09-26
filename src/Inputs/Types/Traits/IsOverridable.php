<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait IsOverridable {

	public function setOverridable($label, $state = true ) {
        $this->setAttribute('overridable', $state);
        $this->setAttribute('prepend', $label);
        $this->setRequired(false);
		return $this;
	}

    public function disableOverridable( ) {
        $this->setAttribute('overridable', false);
        $this->setAttribute('prepend', null);
        return $this;
    }

    public function setOverridden( $state = true ) {
        $this->setAttribute('overridden', $state);
        return $this;
    }

    public function getOverridable() {
		return $this->getAttribute('overridable', false);
	}

    public function getOverridden() {
        return $this->getAttribute('overridden', false);
    }

}
