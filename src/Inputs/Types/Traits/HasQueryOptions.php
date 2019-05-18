<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasQueryOptions {


	/**
	 * @var string The query param
	 */
	protected $query_param;

	/**
	 * @var string The url to query for select options and filter based on the query_param value
	 */
	protected $url;

	/**
	 * @return string
	 */
	public function getQueryParam(): string {
		return $this->query_param;
	}

	/**
	 * @param string $query_param
	 *
	 * @return $this
	 */
	public function setQueryParam( string $query_param = null ) {
		$this->query_param = $query_param;

		return $this;
	}

	/**
	 * @param $url
	 *
	 * @return $this
	 */
	public function setUrl( string $url = null ) {
		$this->url = $url;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string {
		return $this->url;
	}

}