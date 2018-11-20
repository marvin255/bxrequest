<?php

namespace Marvin255\Bxrequest\tests;

use Marvin255\Bxrequest\Response;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

/**
 * Тест для объекта с http ответом.
 */
class ResponseTest extends BaseTestCase
{
    /**
     * Проверяет, что объект выбросит исключение, если в конструкторе
     * указан неверный статус ответа.
     */
    public function testConstructorStatusCodeException()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $this->setExpectedException(InvalidArgumentException::class);
        new Response(20, $stream);
    }

    /**
     * Проверяет, что запрос верно возвращает статус http ответа.
     */
    public function testGetStatusCode()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(501, $stream);

        $this->assertSame(501, $response->getStatusCode());
    }

    /**
     * Проверяет, что запрос верно меняет статус http ответа.
     */
    public function testWithStatus()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(501, $stream);
        $newResponse = $response->withStatus(202, 'test');

        $this->assertNotSame($response, $newResponse);
        $this->assertSame(202, $newResponse->getStatusCode());
        $this->assertSame('test', $newResponse->getReasonPhrase());
    }

    /**
     * Проверяет, что запрос выбросит исключение, если присменестатуса указан
     * неверный код.
     */
    public function testWithStatusStatusException()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(501, $stream);

        $this->setExpectedException(InvalidArgumentException::class);
        $response->withStatus('qwe', 'test');
    }

    /**
     * Проверяет, что запрос верно возвращает версию http.
     */
    public function testGetProtocolVersion()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream);

        $this->assertSame('1.1', $response->getProtocolVersion());
    }

    /**
     * Проверяет, что в запросе можно изменить версию http.
     */
    public function testWithProtocolVersion()
    {
        $version = '1.1';
        $newVersion = '1.0';

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream);
        $newResponse = $response->withProtocolVersion($newVersion);

        $this->assertNotSame($response, $newResponse);
        $this->assertSame($newVersion, $newResponse->getProtocolVersion());
    }

    /*
     * Проверяет, что запрос возвращает массив своих заголовков.
     */
    public function testGetHeaders()
    {
        $serverHeaders = [
            'host' => 'localhost',
            'connection' => 'keep-alive',
            'user-agent' => 'Mozilla/5.0',
        ];
        $etalonHeaders = [
            'host' => [$serverHeaders['host']],
            'connection' => [$serverHeaders['connection']],
            'user-agent' => [$serverHeaders['user-agent']],
        ];

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream, $serverHeaders);
        $testHeaders = $response->getHeaders();

        $this->assertInternalType('array', $testHeaders);
        foreach ($etalonHeaders as $etalonHeaderName => $etalonHeaderValue) {
            $this->assertArrayHasKey($etalonHeaderName, $testHeaders);
            $this->assertSame($etalonHeaderValue, $testHeaders[$etalonHeaderName]);
        }
    }

    /**
     * Проверяет, что запрос проверяет существование заголовка.
     */
    public function testHasHeader()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream, [
            'test' => 'test',
        ]);

        $this->assertTrue($response->hasHeader('TesT'));
        $this->assertFalse($response->hasHeader('TesT-empty'));
    }

    /**
     * Проверяет, что запрос возвращает значение заголовка по имени.
     */
    public function testGetHeader()
    {
        $headerValue = 'header_value_' . mt_rand();

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream, [
            'test' => $headerValue,
        ]);

        $this->assertSame([$headerValue], $response->getHeader('TesT'));
        $this->assertSame([], $response->getHeader('TesT-empty'));
    }

    /**
     * Проверяет, что запрос возвращает значение заголовка по имени в виде строки.
     */
    public function testGetHeaderLine()
    {
        $headerValue = 'header_value_' . mt_rand();

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream, [
            'test' => $headerValue,
        ]);

        $this->assertSame($headerValue, $response->getHeaderLine('TesT'));
        $this->assertSame('', $response->getHeaderLine('TesT-empty'));
    }

    /**
     * Проверяет, что запрос добавляет новый заголовок.
     */
    public function testWithHeader()
    {
        $serverHeaders = [
            'test' => 'test_' . mt_rand(),
        ];
        $newHeader = 'test_new_' . mt_rand();

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream, $serverHeaders);
        $newResponse = $response->withHeader('TEST', $newHeader);

        $this->assertNotSame($response, $newResponse);
        $this->assertSame($newHeader, $newResponse->getHeaderLine('TesT'));
    }

    /**
     * Проверяет, что запрос выбрасывает исключение, если не задано имя заголовка.
     */
    public function testWithHeaderEmptyNameException()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream);

        $this->setExpectedException(InvalidArgumentException::class);
        $response->withHeader('', 'test');
    }

    /**
     * Проверяет, что запрос добавляет новое значение заголовка к уже существующему.
     */
    public function testWithAddedHeader()
    {
        $serverHeaders = [
            'test' => 'test_' . mt_rand(),
        ];
        $newHeaderValue = 'test_new_' . mt_rand();
        $etalonHeader = [$serverHeaders['test'], $newHeaderValue];
        $emptyHeaderValue = 'test_empty_' . mt_rand();
        $emptyEtalonHeader = [$emptyHeaderValue];

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream, $serverHeaders);
        $newResponse = $response->withAddedHeader('TEST', $newHeaderValue);
        $newResponseEmpty = $response->withAddedHeader('TEST-empty', $emptyHeaderValue);

        $this->assertNotSame($response, $newResponse);
        $this->assertSame($etalonHeader, $newResponse->getHeader('TesT'));
        $this->assertNotSame($response, $newResponseEmpty);
        $this->assertSame($emptyEtalonHeader, $newResponseEmpty->getHeader('TesT-Empty'));
    }

    /**
     * Проверяет, что запрос выбрасывает исключение, если не задано имя заголовка.
     */
    public function testWithAddedHeaderEmptyNameException()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream);

        $this->setExpectedException(InvalidArgumentException::class);
        $response->withAddedHeader('', 'test');
    }

    /**
     * Проверяет, что запрос удаляет заголовок из запроса.
     */
    public function testWithoutHeader()
    {
        $serverHeaders = [
            'test' => 'test_' . mt_rand(),
        ];

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream, $serverHeaders);
        $newResponse = $response->withoutHeader('TEST');

        $this->assertNotSame($response, $newResponse);
        $this->assertSame('', $newResponse->getHeaderLine('TesT'));
    }

    /**
     * Проверяет, что запрос возвращает объект StreamInterface.
     */
    public function testGetBody()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream);

        $this->assertSame($stream, $response->getBody());
    }

    /**
     * Проверяет, что запрос заменяет содержимое.
     */
    public function testWithBody()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();
        $newBody = $this->getMockBuilder(StreamInterface::class)->getMock();

        $response = new Response(200, $stream);
        $newResponse = $response->withBody($newBody);

        $this->assertNotSame($response, $newResponse);
        $this->assertSame($newBody, $newResponse->getBody());
    }
}
