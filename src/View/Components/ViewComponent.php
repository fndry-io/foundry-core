<?php

namespace Foundry\Core\View\Components;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

abstract class ViewComponent {

	protected $params = [];

	protected $data = [];

	protected $request;

	abstract static function name() : string;

	abstract public function view() : string;

	public function __construct($params = [], Request $request)
	{
		$this->params = array_merge($this->params, $params);
		$this->request = $request;
	}

	public function init()
	{

	}

	public function permission()
	{
		return true;
	}

	/**
	 * @return View
	 */
	public function render()
	{
		return view($this->view(), $this->data);
	}

	public function set($key, $value = null)
	{
		if (is_array($key)) {
			$this->data = array_merge($this->data, $key);
		} else {
			$this->data[$key] = $value;
		}
	}

	public function get($key, $default = null)
	{
		return Arr::get($this->data, $key, $default);
	}

	public function param($key, $default = null)
	{
		return Arr::get($this->params, $key, $default);
	}
}