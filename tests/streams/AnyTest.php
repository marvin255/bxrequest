<?php

namespace Marvin255\Bxrequest\tests\streams;

use Marvin255\Bxrequest\tests\BaseTestCase;
use Marvin255\Bxrequest\streams\Any;
use InvalidArgumentException;

/**
 * Тест для любого потока, который можно открыть fopen.
 */
class AnyTest extends BaseTestCase
{
    /**
     * Проверяет, что объект выбросит исключение, если в конструкторе
     * указан неверный uri.
     */
    public function testConstructorResourceException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Any('testToString.txt');
    }

    /**
     * Проверяет, что поток сериализуется в строку.
     */
    public function testToString()
    {
        $stream = new Any($this->getPath('testToString.txt'));

        $this->assertSame(
            $this->getFileContents('testToString_expected.txt'),
            (string) $stream
        );

        $stream->close();
    }

    /**
     * Проверяет, что объект верно задает и возвращает смещение указателя.
     */
    public function testSeekAndTell()
    {
        $stream = new Any($this->getPath('testSeekAndTell.txt'));

        $stream->seek(3);
        $readed = $stream->read(3);

        $this->assertSame(6, $stream->tell());
        $this->assertSame(
            trim($this->getFileContents('testSeekAndTell_expected.txt')),
            $readed
        );
        $stream->rewind();
        $this->assertTrue($stream->isSeekable());
        $this->assertSame(0, $stream->tell());

        $stream->close();
    }

    /**
     * Проверяет, что объект веро возвращает флаг об оконочании чтения.
     */
    public function testEof()
    {
        $stream = new Any($this->getPath('testEof.txt'));

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

        $stream->close();
    }

    /**
     * Проверяет, что в данный поток нельзя писать.
     */
    public function testWrite()
    {
        $string = 'test_' . mt_rand();
        $stream = new Any('php://temp', 'r+');

        $this->assertTrue($stream->isWritable());
        $stream->write($string);
        $this->assertSame($string, (string) $stream);
    }

    /**
     * Проверяет, что объект читает содержимое потока в строку.
     */
    public function testRead()
    {
        $stream = new Any($this->getPath('testRead.txt'));
        $stream->read(3);

        $this->assertTrue($stream->isReadable());
        $this->assertSame(
            trim($this->getFileContents('testRead_expected.txt')),
            $stream->read(3)
        );

        $stream->close();
    }

    /**
     * Проверяет, что объект читает все содержимое потока в строку.
     */
    public function testGetContents()
    {
        $stream = new Any($this->getPath('testGetContents.txt'));

        $this->assertSame(
            $this->getFileContents('testGetContents_expected.txt'),
            $stream->getContents()
        );

        $stream->close();
    }

    /**
     * Проверяет, что объектверно возвращает мета данные для потока.
     */
    public function testGetMetadata()
    {
        $stream = new Any($this->getPath('testGetMetadata.txt'));

        $this->assertSame(
            __DIR__ . '/_any_test_fixture/testGetMetadata.txt',
            $stream->getMetadata('uri')
        );
        $this->assertArrayHasKey('uri', $stream->getMetadata());
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
        return file_get_contents(__DIR__ . "/_any_test_fixture/{$fileName}");
    }

    /**
     * Открывает указанный файл на чтение для тетосв.
     *
     * @param string $fileName
     *
     * @return resource
     */
    protected function getPath($fileName)
    {
        return __DIR__ . "/_any_test_fixture/{$fileName}";
    }
}
