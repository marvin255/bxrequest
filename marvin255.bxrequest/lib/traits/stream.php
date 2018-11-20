<?php

namespace Marvin255\Bxrequest\traits;

use RuntimeException;

/**
 * Трэйт для абстрактного потока с данными.
 */
trait Stream
{
    /**
     * @var resource
     */
    protected $streamHandler;

    /**
     * @return string
     */
    public function __toString()
    {
        $contents = false;
        if ($this->streamHandler) {
            rewind($this->streamHandler);
            $contents = stream_get_contents($this->streamHandler);
            rewind($this->streamHandler);
        }

        return $contents === false ? '' : $contents;
    }

    public function close()
    {
        fclose($this->streamHandler);
    }

    /**
     * @return resource
     */
    public function detach()
    {
        $stream = $this->streamHandler;
        $this->streamHandler = null;

        return $stream;
    }

    /**
     * @return int|null
     */
    public function getSize()
    {
        return null;
    }

    /**
     * @return int
     *
     * @throws \RuntimeException
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
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function eof()
    {
        $this->checkStream();

        return feof($this->streamHandler);
    }

    /**
     * @return bool
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * @param int $offset
     * @param int $whence
     *
     * @throws \RuntimeException
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->checkStream();

        if (fseek($this->streamHandler, $offset, $whence) === -1) {
            throw new RuntimeException("Can't seek {$offset} offset in stream");
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function rewind()
    {
        $this->checkStream();

        if (rewind($this->streamHandler) === false) {
            throw new RuntimeException("Can't rewind stream");
        }
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return true;
    }

    /**
     * @param string $string
     *
     * @throws \RuntimeException
     */
    public function write($string)
    {
        $this->checkStream();

        if (fwrite($this->streamHandler, $string) === false) {
            throw new RuntimeException("Can't write string to stream: {$string}");
        }
    }

    /**
     * @return bool
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * @param int $length
     *
     * @return string
     *
     * @throws \RuntimeException
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
     * @return string
     *
     * @throws \RuntimeException
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
        $this->checkStream();

        $return = stream_get_meta_data($this->streamHandler);

        return $key === null ? $return : (isset($return[$key]) ? $return[$key] : null);
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
