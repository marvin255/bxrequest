<?php

namespace Marvin255\Bxrequest;

use Psr\Http\Message\ServerRequestInterface;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Server;
use Marvin255\Bxrequest\streams\Input;
use RuntimeException;

/**
 * Объект с методами для получения данных из http запроса на сервере.
 */
class ServerRequest implements ServerRequestInterface
{
    use traits\Message;
    use traits\Request;
    use traits\ServerRequest;

    /**
     * @var \Bitrix\Main\HttpRequest
     */
    protected $bitrixRequest;

    /**
     * @param \Bitrix\Main\HttpRequest
     * @param \Bitrix\Main\Server
     *
     * @throws \RuntimeException
     */
    public function __construct(HttpRequest $bitrixRequest, Server $bitrixServer)
    {
        $this->loadRequestInfo($bitrixRequest);
        $this->loadServerInfo($bitrixServer);
        $this->loadBody($bitrixRequest, $bitrixServer);
    }

    /**
     * Разбирает объект битрикса с параметрами запроса в переменные для
     * текущего объекта.
     *
     * @param \Bitrix\Main\HttpRequest $request
     */
    protected function loadRequestInfo(HttpRequest $request)
    {
        $this->cookies = $request->getCookieRawList()->toArray();
        $this->queryParams = $request->getQueryList()->toArray();

        $this->requestTarget = $request->getRequestUri();
        $this->method = $request->getRequestMethod();
        $this->uri = new Uri($request->getRequestUri());

        foreach ($request->getHeaders()->toArray() as $header => $headerValue) {
            $this->headers[$header] = is_array($headerValue) ? $headerValue : [$headerValue];
        }
    }

    /**
     * Разбирает объект битрикса с параметрами сервера в переменные для
     * текущего объекта.
     *
     * @param \Bitrix\Main\Server $server
     */
    protected function loadServerInfo(Server $server)
    {
        $this->serverParams = $arServer = $server->toArray();

        if (
            isset($arServer['SERVER_PROTOCOL'])
            && preg_match('#HTTP/(\d{1}\.\d{1})#i', $arServer['SERVER_PROTOCOL'], $matches)
        ) {
            $this->protocolVersion = $matches[1];
        }
    }

    /**
     * Десериализует тело запроса и сохраняет в текущем объекте.
     *
     * @param \Bitrix\Main\HttpRequest $request
     * @param \Bitrix\Main\Server      $server
     *
     * @throws \RuntimeException
     */
    protected function loadBody(HttpRequest $request, Server $server)
    {
        $this->body = new Input(fopen('php://input', 'rb'));

        $postContentTypes = [
            'application/x-www-form-urlencoded',
            'multipart/form-data',
        ];
        $jsonContentTypes = [
            'application/vnd.api+json',
            'application/json',
        ];
        $contentType = $request->getHeader('content-type');
        if (preg_match('#([^;/]+/[^;/]+)#', $contentType, $matches)) {
            $contentType = $matches[1];
        }

        if (in_array($contentType, $postContentTypes) && $request->isPost()) {
            $this->parsedBody = $request->getPostList()->toArray();
        } elseif (in_array($contentType, $jsonContentTypes)) {
            $this->parsedBody = json_decode($request->getInput(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException(
                    'Json decode error: ' . json_last_error_msg()
                );
            }
        }
    }
}
