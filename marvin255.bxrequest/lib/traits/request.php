<?php

namespace Marvin255\Bxrequest\traits;

use Psr\Http\Message\UriInterface;

/**
 * Трэйт для абстрактного запрсоа.
 */
trait Request
{
    /**
     * @var string
     */
    protected $requestTarget = '';
    /**
     * @var string
     */
    protected $method = '';
    /**
     * @var \Psr\Http\Message\UriInterface
     */
    protected $uri;

    /**
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->requestTarget;
    }

    /**
     * @param string $requestTarget
     *
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        $newRequest = clone $this;
        $newRequest->requestTarget = $requestTarget;

        return $newRequest;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return static
     */
    public function withMethod($method)
    {
        $newRequest = clone $this;
        $newRequest->method = $method;

        return $newRequest;
    }

    /**
     * @return \Psr\Http\Message\UriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param \Psr\Http\Message\UriInterface $uri
     * @param bool                           $preserveHost
     *
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $newRequest = clone $this;
        $newRequest->uri = $uri;

        return $newRequest;
    }
}
