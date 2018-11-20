<?php

namespace Marvin255\Bxrequest\traits;

use InvalidArgumentException;

/**
 * Трэйт для абстрактного http ответа.
 */
trait Response
{
    /**
     * @var int
     */
    protected $statusCode = 200;
    /**
     * @var string
     */
    protected $reasonPhrase = 'OK';

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int    $code
     * @param string $reasonPhrase
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $intCode = (int) $code;

        if (!$this->checkStatusCode($intCode)) {
            throw new InvalidArgumentException("Wrong response code: {$code}");
        }

        $newRequest = clone $this;
        $newRequest->statusCode = $intCode;
        $newRequest->reasonPhrase = $reasonPhrase;

        return $newRequest;
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Проверяет код ответа на валидность.
     *
     * @param mixed $statusCode
     *
     * @return bool
     */
    protected function checkStatusCode($statusCode)
    {
        $intCode = (int) $statusCode;

        return $intCode >= 100 && $intCode <= 999;
    }
}
