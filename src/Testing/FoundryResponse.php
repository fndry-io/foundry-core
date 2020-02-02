<?php

namespace Foundry\Core\Testing;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\Assert as PHPUnit;

class FoundryResponse
{
    protected $raw;

    protected $response;

    public function __construct(TestResponse $response)
    {
        $this->raw = $response;
        if ($content = $this->raw->getContent()) {
            $this->response = json_decode($content);
        }
    }

    public function raw()
    {
        return $this->raw;
    }

    public function response()
    {
        return $this->response;
    }

    /**
     * Assert that the response has a 200 status code.
     *
     * @return $this
     */
    public function assertOk()
    {
        PHPUnit::assertTrue(
            $this->response->status,
            $this->getResponseMessage()
        );

        return $this;
    }

    /**
     * Assert that the response has a 200 status code.
     *
     * @return $this
     */
    public function assertNotOk()
    {
        PHPUnit::assertFalse(
            $this->response->status,
            'Foundry response status code [' . $this->response->status . '] does not match expected false.'
        );

        return $this;
    }

    public function getStatus()
    {
        return $this->response->status;
    }

    public function getData()
    {
        return $this->response->data;
    }

    public function getError()
    {
        return $this->response->error;
    }

    public function getMessage()
    {
        return $this->response->message;
    }

    public function assertPaginated()
    {
        $this->assertHasMeta();

        PHPUnit::assertArraySubset(
            ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
            array_keys((array) $this->response->meta),
            'Foundry response does not have the required pagination.'
        );

        return $this;
    }

    public function assertHasMeta()
    {
        PHPUnit::assertObjectHasAttribute(
            'meta',
            $this->response,
            'Foundry response does not have the required meta key.'
        );
    }

    /**
     * @return string
     */
    protected function getResponseMessage(): string
    {
        $string = "Foundry response status does not match expected true. Response was:";
        if (isset($this->response->status)) {
            $string .= "\r\nStatus: " . ($this->response->status) ? 'true' : 'false';
        }
        if (isset($this->response->code)) {
            $string .= "\r\nCode: " . $this->response->code;
        }
        if (isset($this->response->error)) {
            $string .= "\r\nError: " . $this->response->error;
        }
        if (isset($this->response->message)) {
            $string .= "\r\nMessage: " . $this->response->message;
        }
        if (isset($this->response->code) && $this->response->code === 422) {
            $string .= "\r\nData: " . json_encode($this->response->data);
        }
        return $string . "\r\n";
    }


}
