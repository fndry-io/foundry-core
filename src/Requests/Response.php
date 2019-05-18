<?php

namespace Foundry\Core\Requests;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

/**
 * Foundry Simple Response Object
 *
 * This object will encapsulate and standardise the responses from a service
 *
 * @package Foundry\Requests
 */
class Response {

	protected $status;

	protected $data = [];

	protected $code;

	protected $error;

	protected $message;

	/**
	 * Response Constructor
	 *
	 * @param mixed $data
	 * @param bool $status
	 * @param int $code
	 * @param array|string|null $error
	 * @param string|null $message
	 */
	public function __construct( $data = [], $status = true, $code = 200, $error = null, $message = null ) {
		$this->data    = $data;
		$this->status  = $status;
		$this->code    = $code;
		$this->error   = $error;
		$this->message = $message;
	}

	/**
	 * Is the response a success
	 *
	 * @return bool
	 */
	public function isSuccess() {
		return $this->status;
	}

	/**
	 * Success response
	 *
	 * @param array $data
	 *
	 * @return Response
	 */
	static function success( $data = [], $message = null ) {
		return new Response( $data, true, 200, null, $message );
	}

	/**
	 * Redirect response
	 *
	 * @param string $url
	 *
	 * @return Response
	 */
	static function redirect( $url, $message = null ) {
		return new Response( $url, true, 301, null, $message );
	}

	/**
     * Error response
     *
     * @param $error
     * @param $code
     *
     * @param array $data
     * @return Response
     */
	static function error( $error, $code , $data = []) {
		return new Response( $data, false, $code, $error );
	}

	/**
	 * Convert the response to an array
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		$array = [
			'status' => $this->status,
			'code'   => $this->code,
			'data'   => $this->data
		];
		if ( $this->error ) {
			$array['error'] = $this->error;
		}
		if ( $this->message ) {
			$array['message'] = $this->message;
		}

		return $array;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getError() {
		return $this->error;
	}

	public function getCode() {
		return $this->code;
	}

	public function getData($key = null, $default = null) {
		if ($key) {
			return Arr::get($this->data, $key, $default);
		} else {
			return $this->data;
		}
	}

	public function getMessage() {
		return $this->message;
	}

	public function __toString() {
		return json_encode( $this->jsonSerialize() );
	}

	public function redirectResponse( RedirectResponse $redirect ) {
		if ( $this->getCode() == 422 ) {
			$redirect->withErrors( $this->getError() );
		} else {
			$redirect->with( 'status', $this->getError() );
		}

		return $redirect;
	}

	public function toJsonResponse()
	{
		return response()->json($this->jsonSerialize());
	}

}
