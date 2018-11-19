<?php

namespace Marvin255\Bxrequest\streams;

use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Класс для потока, который читает данные из php://input.
 */
class Input implements StreamInterface
{
    /**
     * @var resource
     */
    protected $streamHandler;

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
    public function __toString()
    {
        $contents = false;
        if ($this->streamHandler) {
            $contents = stream_get_contents($this->streamHandler);
        }

        return $contents === false ? '' : $contents;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        fclose($this->streamHandler);
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        $stream = $this->streamHandler;
        $this->streamHandler = null;

        return $stream;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function tell()
    {
        $this->checkStream();

        $tellRes = ftell($this->streamHandler);
        if ($tellRes === false) {
            throw new RuntimeException("Can't tell position for stream");
        }

        return $tellRes;
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        $this->checkStream();

        return feof($this->streamHandler);
    }

    /**
     * @inheritdoc
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->checkStream();

        if (fseek($this->streamHandler, $offset, $whence) === -1) {
            throw new RuntimeException("Can't seek {$offset} offset in stream");
        }
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->checkStream();

        if (rewind($this->streamHandler) === false) {
            throw new RuntimeException("Can't rewind stream");
        }
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

    /**
     * @inheritdoc
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function read($length)
    {
        $this->checkStream();

        $readRes = fread($this->streamHandler, $length);
        if ($readRes === false) {
            throw new RuntimeException("Can't read {$length} bytes from stream");
        }

        return $readRes;
    }

    /**
     * @inheritdoc
     */
    public function getContents()
    {
        $contents = stream_get_contents($this->streamHandler);
        if ($contents === false) {
            throw new RuntimeException('Unable to read stream contents');
        }

        return $contents;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
    }

    /**
     * Проверяет поток на готовность к работе.
     *
     * @throws \RuntimeException
     */
    protected function checkStream()
    {
        if (!$this->streamHandler) {
            throw new RuntimeException('Stream is in an unusable state');
        }
    }
}
