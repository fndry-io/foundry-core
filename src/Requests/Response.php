<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Support\InputTypeCollection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

	protected $filters;

	protected $additional = [];

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
	static function success( $data = [], $message = null, $code = 200 ) {
		return new Response( $data, true, $code, null, $message );
	}

	/**
	 * @param $resource
	 * @param null $message
	 *
	 * @return Response
	 */
	static function resource( JsonResource $resource, $message = null ){
		return self::success( $resource, $message );
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
     * Add additional meta data to the resource response.
     *
     * @param  array  $data
     * @return $this
     */
    public function additional(array $data)
    {
        $this->additional = $data;

        return $this;
    }

	/**
	 * Convert the response to an array
	 *
	 * @return array
	 */
	public function jsonSerialize() {
        $array = $this->toResponseArray();

		$data = $this->data;
		if ($data instanceof \JsonSerializable) {
			$data = $data->jsonSerialize();
		} elseif ($data instanceof Arrayable) {
			$data = $data->toArray();
		}
		$array['data'] = $data;

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

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
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

	public function toJsonResponse($request)
	{
		if ($this->data instanceof LengthAwarePaginator) {
		    //we need to standardise the pagination to our format
		    $response = $this->toResponseArray();
		    $additional = $this->data->toArray();
            $response['data'] = $additional['data'];
		    unset($additional['data']);
            $response['meta'] = array_merge($response['meta'], $additional);

            return new JsonResponse($response);
		} elseif ($this->data instanceof JsonResource) {
            $array = $this->toResponseArray();
            $this->data->additional($array);
            return $this->data->toResponse($request);
        } else {
            return new JsonResponse($this->jsonSerialize());
		}
	}

	/**
	 * @param \Throwable $exception
	 *
	 * @return Response
	 */
	static function fromException(\Throwable $exception) : Response
	{
		$error = $exception->getMessage();

		if (isset($exception->status)) {
			$code = $exception->status;
		} else {
			$code = $exception->getCode();
		}

		$data = null;

		if (config('app.debug')) {
			$data = [
				'line' => $exception->getLine(),
				'file' => $exception->getFile(),
				'trace' => $exception->getTraceAsString()
			];
		}

		if ($exception instanceof ValidationException) {
			$error = $exception->getMessage();
			$code = 422;
			$data = $exception->errors();
		} else if ($exception instanceof HttpException) {
			$error = $exception->getMessage();
			$code = $exception->getStatusCode();
		} else if ($exception instanceof AuthenticationException){
			$code = 401;
		} else if ($exception instanceof AuthorizationException){
			$code = 403;
		} else if ($exception instanceof QueryException && !config('app.debug')) {
            $error = __('Internal Error Occurred');
        }

		return static::error($error, $code, $data);
	}

    /**
     * @param JsonResource|string $class
     * @param bool $collection
     *
     * @return $this
     */
	public function asResource($class, $collection = false)
    {
        if ($this->status) {
            if ($collection) {
                $this->data = $class::collection($this->data);
            } else {
                $this->data = new $class($this->data);
            }
        }
        return $this;
    }

    public function withFilters(InputTypeCollection $filters)
    {
        $this->additional['filters'] = $filters;
        return $this;
    }

    public function withMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return array
     */
    protected function toResponseArray(): array
    {
        $array = [
            'status' => $this->status,
            'code' => $this->code
        ];
        if ($this->additional) {
            $array['meta'] = $this->additional;
        }
        if ($this->error) {
            $array['error'] = $this->error;
        }
        if ($this->message) {
            $array['message'] = $this->message;
        }
        if ($this->filters) {
            if ($this->filters instanceof Collection) {
                $array['filters'] = $this->filters->jsonSerialize();
            }
        }
        return $array;
    }
}
