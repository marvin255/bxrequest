<?php

namespace Marvin255\Bxrequest;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Объект для стандартного http ответа.
 */
class ServerRequest implements ResponseInterface
{
    use traits\Message;
    use traits\Response;

    /**
     * @param int             $status
     * @param StreamInterface $body
     * @param array           $headers
     */
    public function __construct($status, StreamInterface $body, array $headers = [])
    {
        if (!$this->checkStatusCode($status)) {
            throw new InvalidArgumentException("Wrong response code: {$code}");
        }

        $this->statusCode = (int) $status;
        $this->body = $body;
        $this->headers = $this->normalizeHeadersList($headers);
    }
}
