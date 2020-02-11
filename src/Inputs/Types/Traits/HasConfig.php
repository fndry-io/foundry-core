<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Illuminate\Support\Arr;

trait HasConfig {

	/**
	 * @return string
	 */
	public function getConfig(): string {
		return $this->getAttribute('config');
	}

	/**
	 * @param mixed $value
     * @param string|null $key the key in the config to set
	 *
	 * @return $this
	 */
	public function setConfig( string $key, $value = null ) {
	    if (!isset($this->attributes['config'])) {
            $this->attributes['config'] = [];
        }
        Arr::set($this->attributes['config'], $key, $value);
		return $this;
	}

    /**
     * Remove a given config entry
     *
     * @param string $key
     * @return $this
     */
	public function removeConfig(string $key)
    {
        array_forget($this->attributes['config'], $key);
        return $this;
    }

}
