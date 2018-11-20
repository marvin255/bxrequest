<?php

namespace Marvin255\Bxrequest\bitrix;

use Bitrix\Main\Type;

/**
 * Класс, который расширяет стандартный HttpRequest методом для получения
 * заголовков запроса.
 */
class HttpRequest extends \Bitrix\Main\HttpRequest
{
    /**
     * @var Type\ParameterDictionary
     */
    protected $headers;

    /**
     * Creates new HttpRequest object.
     *
     * @param Server $server
     * @param array  $queryString _GET
     * @param array  $postData    _POST
     * @param array  $files       _FILES
     * @param array  $cookies     _COOKIE
     */
    public function __construct(Server $server, array $queryString, array $postData, array $files, array $cookies)
    {
        parent::__construct($server, $queryString, $postData, $files, $cookies);
        $this->headers = new Type\ParameterDictionary($this->fetchHeaders($server));
    }

    /**
     * Applies filter to the http request data. Preserve original values.
     *
     * @param Type\IRequestFilter $filter Filter object
     */
    public function addFilter(Type\IRequestFilter $filter)
    {
        $filteredValues = $filter->filter([
            'get' => $this->queryString->values,
            'post' => $this->postData->values,
            'files' => $this->files->values,
            'headers' => $this->headers->values,
            'cookie' => $this->cookiesRaw->values,
        ]);

        if (isset($filteredValues['get'])) {
            $this->queryString->setValuesNoDemand($filteredValues['get']);
        }
        if (isset($filteredValues['post'])) {
            $this->postData->setValuesNoDemand($filteredValues['post']);
        }
        if (isset($filteredValues['files'])) {
            $this->files->setValuesNoDemand($filteredValues['files']);
        }
        if (isset($filteredValues['headers'])) {
            $this->headers->setValuesNoDemand($this->normalizeHeaders($filteredValues['headers']));
        }
        if (isset($filteredValues['cookie'])) {
            $this->cookiesRaw->setValuesNoDemand($filteredValues['cookie']);
            $this->cookies = new Type\ParameterDictionary($this->prepareCookie($filteredValues['cookie']));
        }

        if (isset($filteredValues['get']) || isset($filteredValues['post'])) {
            $this->values = array_merge($this->queryString->values, $this->postData->values);
        }
    }

    /**
     * Returns the header of the current request.
     *
     * @param string $name name of header
     *
     * @return null|string
     */
    public function getHeader($name)
    {
        return $this->headers->get(strtolower($name));
    }

    /**
     * Returns the list of headers of the current request.
     *
     * @return Type\ParameterDictionary
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param Server $server
     *
     * @return array
     */
    private function fetchHeaders(Server $server)
    {
        $headers = [];
        foreach ($server as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $headerName = substr($name, 5);
                $headers[$headerName] = $value;
            }
        }

        return $this->normalizeHeaders($headers);
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function normalizeHeaders(array $headers)
    {
        $normalizedHeaders = [];
        foreach ($headers as $name => $value) {
            $headerName = strtolower(str_replace('_', '-', $name));
            $normalizedHeaders[$headerName] = $value;
        }

        return $normalizedHeaders;
    }
}
