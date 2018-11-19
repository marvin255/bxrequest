<?php

namespace Marvin255\Bxrequest;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Server;
use Marvin255\Bxrequest\streams\Input;

/**
 * Объект с методами для получения данных из http запроса на сервере.
 */
class ServerRequest implements ServerRequestInterface
{
    use \Marvin255\Bxrequest\traits\Message;

    /**
     * @var \Bitrix\Main\HttpRequest
     */
    protected $bitrixRequest;

    /**
     * @param \Bitrix\Main\HttpRequest
     * @param \Bitrix\Main\Server
     */
    public function __construct(HttpRequest $bitrixRequest, Server $bitrixServer)
    {
        $this->bitrixRequest = $bitrixRequest;
        $this->loadServerInfo($bitrixServer);
    }

    /**
     * @inheritdoc
     */
    public function getServerParams()
    {
    }

    /**
     * @inheritdoc
     */
    public function getCookieParams()
    {
    }

    /**
     * @inheritdoc
     */
    public function withCookieParams(array $cookies)
    {
    }

    /**
     * @inheritdoc
     */
    public function getQueryParams()
    {
    }

    /**
     * @inheritdoc
     */
    public function withQueryParams(array $query)
    {
    }

    /**
     * @inheritdoc
     */
    public function getUploadedFiles()
    {
    }

    /**
     * @inheritdoc
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
    }

    /**
     * @inheritdoc
     */
    public function getParsedBody()
    {
    }

    /**
     * @inheritdoc
     */
    public function withParsedBody($data)
    {
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
    }

    /**
     * @inheritdoc
     */
    public function getAttribute($name, $default = null)
    {
    }

    /**
     * @inheritdoc
     */
    public function withAttribute($name, $value)
    {
    }

    /**
     * @inheritdoc
     */
    public function withoutAttribute($name)
    {
    }

    /**
     * @inheritdoc
     */
    public function getRequestTarget()
    {
    }

    /**
     * @inheritdoc
     */
    public function withRequestTarget($requestTarget)
    {
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
    }

    /**
     * @inheritdoc
     */
    public function withMethod($method)
    {
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
    }

    /**
     * @inheritdoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
    }

    /**
     * Разбирает объект битрикса с параметрами запроса в переменные для
     * текущего объекта.
     *
     * @param \Bitrix\Main\Server $server
     */
    protected function loadServerInfo(Server $server)
    {
        $arServer = $server->toArray();

        if (
            isset($arServer['SERVER_PROTOCOL'])
            && preg_match('#HTTP/(\d{1}\.\d{1})#i', $arServer['SERVER_PROTOCOL'], $matches)
        ) {
            $this->protocolVersion = $matches[1];
        }

        foreach ($arServer as $key => $value) {
            if (strpos($key, 'HTTP_') !== 0) {
                continue;
            }
            $headerName = str_replace('_', '-', substr($key, 5));
            $headerName = $this->normilizeHeaderName($headerName);
            $this->headers[$headerName] = [$value];
        }

        $this->body = new Input(fopen('php://input', 'rb'));
    }
}
