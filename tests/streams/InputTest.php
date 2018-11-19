<?php

namespace Marvin255\Bxrequest\tests\streams;

use Marvin255\Bxrequest\tests\BaseTestCase;
use Marvin255\Bxrequest\streams\Input;
use InvalidArgumentException;
use RuntimeException;

/**
 * Тест для потока, который читает данные из php://input.
 */
class InputTest extends BaseTestCase
{
    /**
     * Проверяет, что объект выбросит исключение, если в конструкторе
     * указан не ресурс.
     */
    public function testConstructorResourceException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Input('testToString.txt');
    }

    /**
     * Проверяет, что объект выбрасывет исключение, если ресурс потока php
     * отключен.
     */
    public function testCheckStreamException()
    {
        $stream = new Input($this->createHandler('testToString.txt'));
        $stream->detach();

        $this->setExpectedException(RuntimeException::class);
        $stream->read(1024);
    }

    /**
     * Проверяет, что поток сериализуется в строку.
     */
    public function testToString()
    {
        $stream = new Input($this->createHandler('testToString.txt'));

        $this->assertSame(
            $this->getFileContents('testToString_expected.txt'),
            (string) $stream
        );
    }

    /**
     * Проверяет, что объект верно задает и возвращает смещение указателя.
     */
    public function testSeekAndTell()
    {
        $stream = new Input($this->createHandler('testSeekAndTell.txt'));

        $stream->seek(3);
        $readed = $stream->read(3);

        $this->assertSame(6, $stream->tell());
        $this->assertSame(
            trim($this->getFileContents('testSeekAndTell_expected.txt')),
            $readed
        );
        $stream->rewind();
        $this->assertSame(0, $stream->tell());
    }

    /**
     * Проверяет, что объект веро возвращает флаг об оконочании чтения.
     */
    public function testEof()
    {
        $stream = new Input($this->createHandler('testEof.txt'));

        $this->assertFalse($stream->eof());

        $readed = '';
        while (!$stream->eof()) {
            $readed .= $stream->read(1);
        }

        $this->assertTrue($stream->eof());
        $this->assertSame(
            $this->getFileContents('testEof_expected.txt'),
            $readed
        );
    }

    /**
     * Проверяет, что объект читает содержимое потока в строку.
     */
    public function testRead()
    {
        $stream = new Input($this->createHandler('testRead.txt'));
        $stream->read(3);

        $this->assertSame(
            trim($this->getFileContents('testRead_expected.txt')),
            $stream->read(3)
        );
    }

    /**
     * Проверяет, что объект читает все содержимое потока в строку.
     */
    public function testGetContents()
    {
        $stream = new Input($this->createHandler('testGetContents.txt'));

        $this->assertSame(
            $this->getFileContents('testGetContents_expected.txt'),
            $stream->getContents()
        );
    }

    /**
     * Возвращает содержимое файла с фикстурой.
     *
     * @param string $fileName
     *
     * @return string
     */
    protected function getFileContents($fileName)
    {
        return file_get_contents(__DIR__ . "/_fixture/{$fileName}");
    }

    /**
     * Открывает указанный файл на чтение для тетосв.
     *
     * @param string $fileName
     *
     * @return resource
     */
    protected function createHandler($fileName)
    {
        return fopen(__DIR__ . "/_fixture/{$fileName}", 'rb');
    }
}
