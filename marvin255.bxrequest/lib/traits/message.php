<?php

namespace Marvin255\Bxrequest\traits;

use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

/**
 * Трэйт для абстрактного http сообщения.
 */
trait Message
{
    /**
     * @var string
     */
    protected $protocolVersion = '1.1';
    /**
     * @var array
     */
    protected $headers = [];
    /**
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $body;

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @return static
     */
    public function withProtocolVersion($version)
    {
        $newRequest = clone $this;
        $newRequest->protocolVersion = $version;

        return $newRequest;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader($name)
    {
        return (bool) $this->searchHeaderInternalIndex($name);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getHeader($name)
    {
        $internalIndex = $this->searchHeaderInternalIndex($name);

        return $internalIndex ? $this->headers[$internalIndex] : [];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getHeaderLine($name)
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public function withHeader($name, $value)
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException("Header name can't be empty");
        }

        $normalizedValue = $this->normalizeHeaderValue($value);

        $headers = $this->headers;
        if ($internalIndex = $this->searchHeaderInternalIndex($name)) {
            unset($headers[$internalIndex]);
        }
        $headers[$name] = $normalizedValue;

        $newRequest = clone $this;
        $newRequest->headers = $headers;

        return $newRequest;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public function withAddedHeader($name, $value)
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException("Header name can't be empty");
        }

        $normalizedValue = $this->normalizeHeaderValue($value);

        $headers = $this->headers;
        if ($internalIndex = $this->searchHeaderInternalIndex($name)) {
            $headers[$internalIndex] = array_merge(
                $headers[$internalIndex],
                $normalizedValue
            );
        } else {
            $headers[$name] = $normalizedValue;
        }

        $newRequest = clone $this;
        $newRequest->headers = $headers;

        return $newRequest;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function withoutHeader($name)
    {
        $name = trim($name);

        $headers = $this->headers;
        if ($internalIndex = $this->searchHeaderInternalIndex($name)) {
            unset($headers[$internalIndex]);
        }

        $newRequest = clone $this;
        $newRequest->headers = $headers;

        return $newRequest;
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param \Psr\Http\Message\StreamInterface $body
     *
     * @return static
     */
    public function withBody(StreamInterface $body)
    {
        $newRequest = clone $this;
        $newRequest->body = $body;

        return $newRequest;
    }

    /**
     * Ищет под каким именно именем хранится заголовок в данном объекте.
     * По условиям мы не должныменять регистр для заголовков, тем не менее поиск
     * должен оставаться регистронезависимым.
     *
     * @param string $toSearch
     *
     * @return string|null
     */
    protected function searchHeaderInternalIndex($header)
    {
        $normalizedSearchName = $this->normalizeHeaderName($header);
        $return = null;

        foreach ($this->headers as $header => $value) {
            if ($this->normalizeHeaderName($header) === $normalizedSearchName) {
                $return = $header;
                break;
            }
        }

        return $return;
    }

    /**
     * Приводит массив заголовков к общему виду.
     *
     * @param array $headers
     *
     * @return array
     */
    protected function normalizeHeadersList(array $headers)
    {
        $return = [];

        foreach ($headers as $headerName => $headerValue) {
            $normalizedName = $this->normalizeHeaderName($headerName);
            $normalizedValue = $this->normalizeHeaderValue($headerValue);
            $return[$normalizedName] = $normalizedValue;
        }

        return $return;
    }

    /**
     * Нормализует название заголовка для поиска.
     *
     * @param string $header
     *
     * @return string
     */
    protected function normalizeHeaderName($header)
    {
        return strtolower($header);
    }

    /**
     * Нормализует значение заголовка.
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function normalizeHeaderValue($value)
    {
        $normalized = is_array($value) ? array_keys($value) : [$value];

        return array_map('trim', $normalized);
    }
}
