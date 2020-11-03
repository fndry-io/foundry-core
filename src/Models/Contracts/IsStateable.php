<?php

namespace Foundry\Core\Models\Contracts;

interface IsStateable {

	static function getStateLabels() : array;

	static function getStateLabel($key) : string;

	public function setState($state);

	public function states();

	public function state();

}
