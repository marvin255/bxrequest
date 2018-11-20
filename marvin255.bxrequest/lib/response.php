<?php

namespace Marvin255\Bxrequest;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

/**
 * Объект для стандартного http ответа.
 */
class Response implements ResponseInterface
{
    use traits\Message;
    use traits\Response;

    /**
     * @param int                               $statusCode
     * @param \Psr\Http\Message\StreamInterface $body
     * @param array                             $headers
     */
    public function __construct($statusCode, StreamInterface $body, array $headers = [])
    {
        if (!$this->checkStatusCode($statusCode)) {
            throw new InvalidArgumentException("Wrong response code: {$statusCode}");
        }

        $this->statusCode = (int) $statusCode;
        $this->body = $body;
        $this->headers = $this->normalizeHeadersList($headers);
    }
}
