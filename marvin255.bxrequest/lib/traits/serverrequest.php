<?php

namespace Marvin255\Bxrequest\traits;

use RuntimeException;

/**
 * Трэйт для представления запроса, котоый пришел на сервер.
 */
trait ServerRequest
{
    /**
     * @var array
     */
    protected $serverParams = [];
    /**
     * @var array
     */
    protected $cookies = [];
    /**
     * @var array
     */
    protected $queryParams = [];
    /**
     * @var mixed
     */
    protected $parsedBody;
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookies;
    }

    /**
     * @param array $cookies
     *
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $newRequest = clone $this;
        $newRequest->cookies = $cookies;

        return $newRequest;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param array $query
     *
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $newRequest = clone $this;
        $newRequest->queryParams = $query;

        return $newRequest;
    }

    /**
     * @inheritdoc
     */
    public function getUploadedFiles()
    {
        throw new RuntimeException('Method realisation still in progress');
    }

    /**
     * @inheritdoc
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        throw new RuntimeException('Method realisation still in progress');
    }

    /**
     * @return mixed
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @param mixed $data
     *
     * @return static
     */
    public function withParsedBody($data)
    {
        $newRequest = clone $this;
        $newRequest->parsedBody = $data;

        return $newRequest;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name])
            ? $this->attributes[$name]
            : $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $newRequest = clone $this;
        $newRequest->attributes = array_merge($this->attributes, [$name => $value]);

        return $newRequest;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function withoutAttribute($name)
    {
        $attributes = $this->attributes;
        unset($attributes[$name]);

        $newRequest = clone $this;
        $newRequest->attributes = $attributes;

        return $newRequest;
    }
}
