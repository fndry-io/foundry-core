<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasQueryOptions {

	/**
	 * @return string
	 */
	public function getQueryParam(): string {
		return $this->getAttribute('query');
	}

	/**
	 * @param string $query
	 *
	 * @return $this
	 */
	public function setQueryParam( string $query = null ) {
		$this->setAttribute('query', $query);

		return $this;
	}

	/**
	 * @param $url
	 *
	 * @return $this
	 */
	public function setUrl( string $url = null ) {
		$this->setAttribute('url', $url);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string {
		return $this->getAttribute('url');
	}

	/**
	 * @param string $url
	 * @param string $query_param
	 *
	 * @return $this
	 */
	public function setQuery(string $url, string $query_param = 'q') {
		$this->setUrl($url);
		$this->setQueryParam($query_param);
		return $this;
	}

}