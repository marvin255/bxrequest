<?php

namespace Marvin255\Bxrequest\streams;

use Psr\Http\Message\StreamInterface;
use Marvin255\Bxrequest\traits;
use InvalidArgumentException;

/**
 * Класс для любого потока, который можно открыть fopen.
 */
class Any implements StreamInterface
{
    use traits\Stream;

    /**
     * @param string $uri
     * @param string $mode
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($uri, $mode = 'r+')
    {
        $streamHandler = @fopen($uri, $mode);
        if (!is_resource($streamHandler)) {
            throw new InvalidArgumentException(
                "Can't open {$uri} in {$mode} mode"
            );
        }

        $this->streamHandler = $streamHandler;
    }
}
