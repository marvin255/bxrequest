<?php

namespace Marvin255\Bxrequest\streams;

use Psr\Http\Message\StreamInterface;
use Marvin255\Bxrequest\traits;
use InvalidArgumentException;
use RuntimeException;

/**
 * Класс для потока, который читает данные из php://input.
 */
class Input implements StreamInterface
{
    use traits\Stream;

    /**
     * @param resource $streamHandler
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($streamHandler)
    {
        if (!is_resource($streamHandler)) {
            throw new InvalidArgumentException(
                'Stream handler must be instance of php resource'
            );
        }

        $this->streamHandler = $streamHandler;
    }

    /**
     * @inheritdoc
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function write($string)
    {
        throw new RuntimeException('Stream is not writable');
    }
}
