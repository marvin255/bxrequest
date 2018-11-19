<?php

namespace Marvin255\Bxrequest\tests;

use Marvin255\Bxrequest\ServerRequest;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

/**
 * Тест для объекта с методами для получения данных из http запроса на сервере.
 */
class ServerRequestTest extends BaseTestCase
{
    /**
     * Проверяет, что объект возвращает правильную цель запроса.
     */
    public function testGetRequestTarget()
    {
        $target = '/test/' . mt_rand(0, 1);

        $bxRequest = $this->createRequestMock(['getRequestUri' => $target]);
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);

        $this->assertSame($target, $request->getRequestTarget());
    }

    /**
     * Проверяет, что объект задает новую цель запроса.
     */
    public function testWithRequestTarget()
    {
        $target = '/test/' . mt_rand(0, 1);
        $newTarget = '/test_new/' . mt_rand(0, 1);

        $bxRequest = $this->createRequestMock(['getRequestUri' => $target]);
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);
        $newRequest = $request->withRequestTarget($newTarget);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($newTarget, $newRequest->getRequestTarget());
    }

    /**
     * Проверяет, что объект возвращает правильную цель запроса.
     */
    public function testGetMethod()
    {
        $method = 'DELETE';

        $bxRequest = $this->createRequestMock(['getRequestMethod' => $method]);
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);

        $this->assertSame($method, $request->getMethod());
    }

    /**
     * Проверяет, что объект задает новый метод запроса.
     */
    public function testWithMethod()
    {
        $method = 'DELETE';
        $newMethod = 'POST';

        $bxRequest = $this->createRequestMock(['getRequestMethod' => $method]);
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);
        $newRequest = $request->withMethod($newMethod);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($newMethod, $newRequest->getMethod());
    }

    /**
     * Проверяет, что объект возвращает uri.
     */
    public function testGetUri()
    {
        $target = '/test/' . mt_rand(0, 1);

        $bxRequest = $this->createRequestMock(['getRequestUri' => $target]);
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);

        $this->assertSame($target, $request->getUri()->getPath());
    }

    /**
     * Проверяет, что объект возвращает uri.
     */
    public function testWithUri()
    {
        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock();
        $newUri = $this->getMockBuilder(UriInterface::class)->getMock();

        $request = new ServerRequest($bxRequest, $server);
        $newRequest = $request->withUri($newUri);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($newUri, $newRequest->getUri());
    }

    /**
     * Проверяет, что запрос верно возвращает версию http.
     */
    public function testGetProtocolVersion()
    {
        $version = '1.' . mt_rand(0, 1);

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock(['SERVER_PROTOCOL' => "HTTP/{$version}"]);

        $request = new ServerRequest($bxRequest, $server);

        $this->assertSame($version, $request->getProtocolVersion());
    }

    /**
     * Проверяет, что в запросе можно изменить версию http.
     */
    public function testWithProtocolVersion()
    {
        $version = '1.1';
        $newVersion = '1.0';

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock(['SERVER_PROTOCOL' => "HTTP/{$version}"]);

        $request = new ServerRequest($bxRequest, $server);
        $newRequest = $request->withProtocolVersion($newVersion);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($newVersion, $newRequest->getProtocolVersion());
    }

    /**
     * Проверяет, что запрос возвращает массив своих заголовков.
     */
    public function testGetHeaders()
    {
        $serverHeaders = [
            'HTTP_HOST' => 'localhost',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_USER_AGENT' => 'Mozilla/5.0',
        ];
        $etalonHeaders = [
            'host' => [$serverHeaders['HTTP_HOST']],
            'connection' => [$serverHeaders['HTTP_CONNECTION']],
            'user-agent' => [$serverHeaders['HTTP_USER_AGENT']],
        ];

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock($serverHeaders);

        $request = new ServerRequest($bxRequest, $server);
        $testHeaders = $request->getHeaders();

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
        $serverHeaders = [
            'HTTP_TEST' => 'test_' . mt_rand(),
        ];

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock($serverHeaders);

        $request = new ServerRequest($bxRequest, $server);

        $this->assertTrue($request->hasHeader('TesT'));
        $this->assertFalse($request->hasHeader('TesT-empty'));
    }

    /**
     * Проверяет, что запрос возвращает значение заголовка по имени.
     */
    public function testGetHeader()
    {
        $serverHeaders = [
            'HTTP_TEST' => 'test_' . mt_rand(),
        ];
        $etalonHeader = [$serverHeaders['HTTP_TEST']];

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock($serverHeaders);

        $request = new ServerRequest($bxRequest, $server);

        $this->assertSame($etalonHeader, $request->getHeader('TesT'));
        $this->assertSame([], $request->getHeader('TesT-empty'));
    }

    /**
     * Проверяет, что запрос возвращает значение заголовка по имени в виде строки.
     */
    public function testGetHeaderLine()
    {
        $serverHeaders = [
            'HTTP_TEST' => 'test_' . mt_rand(),
        ];
        $etalonHeader = $serverHeaders['HTTP_TEST'];

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock($serverHeaders);

        $request = new ServerRequest($bxRequest, $server);

        $this->assertSame($etalonHeader, $request->getHeaderLine('TesT'));
        $this->assertSame('', $request->getHeaderLine('TesT-empty'));
    }

    /**
     * Проверяет, что запрос добавляет новый заголовок.
     */
    public function testWithHeader()
    {
        $serverHeaders = [
            'HTTP_TEST' => 'test_' . mt_rand(),
        ];
        $newHeader = 'test_new_' . mt_rand();

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock($serverHeaders);

        $request = new ServerRequest($bxRequest, $server);
        $newRequest = $request->withHeader('TEST', $newHeader);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($newHeader, $newRequest->getHeaderLine('TesT'));
    }

    /**
     * Проверяет, что запрос выбрасывает исключение, если не задано имя заголовка.
     */
    public function testWithHeaderEmptyNameException()
    {
        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);

        $this->setExpectedException(InvalidArgumentException::class);
        $request->withHeader('', 'test');
    }

    /**
     * Проверяет, что запрос добавляет новое значение заголовка к уже существующему.
     */
    public function testWithAddedHeader()
    {
        $serverHeaders = [
            'HTTP_TEST' => 'test_' . mt_rand(),
        ];
        $newHeaderValue = 'test_new_' . mt_rand();
        $etalonHeader = [$serverHeaders['HTTP_TEST'], $newHeaderValue];
        $emptyHeaderValue = 'test_empty_' . mt_rand();
        $emptyEtalonHeader = [$emptyHeaderValue];

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock($serverHeaders);

        $request = new ServerRequest($bxRequest, $server);
        $newRequest = $request->withAddedHeader('TEST', $newHeaderValue);
        $newRequestEmpty = $request->withAddedHeader('TEST-empty', $emptyHeaderValue);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($etalonHeader, $newRequest->getHeader('TesT'));
        $this->assertNotSame($request, $newRequestEmpty);
        $this->assertSame($emptyEtalonHeader, $newRequestEmpty->getHeader('TesT-Empty'));
    }

    /**
     * Проверяет, что запрос выбрасывает исключение, если не задано имя заголовка.
     */
    public function testWithAddedHeaderEmptyNameException()
    {
        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);

        $this->setExpectedException(InvalidArgumentException::class);
        $request->withAddedHeader('', 'test');
    }

    /**
     * Проверяет, что запрос удаляет заголовок из запроса.
     */
    public function testWithoutHeader()
    {
        $serverHeaders = [
            'HTTP_TEST' => 'test_' . mt_rand(),
        ];

        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock($serverHeaders);

        $request = new ServerRequest($bxRequest, $server);
        $newRequest = $request->withoutHeader('TEST');

        $this->assertNotSame($request, $newRequest);
        $this->assertSame('', $newRequest->getHeaderLine('TesT'));
    }

    /**
     * Проверяет, что запрос возвращает объект StreamInterface.
     */
    public function testGetBody()
    {
        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);

        $this->assertInstanceOf(StreamInterface::class, $request->getBody());
    }

    /**
     * Проверяет, что запрос заменяет содержимое.
     */
    public function testWithBody()
    {
        $bxRequest = $this->createRequestMock();
        $server = $this->createServerMock();

        $request = new ServerRequest($bxRequest, $server);

        $newBody = $this->getMockBuilder(StreamInterface::class)->getMock();
        $newRequest = $request->withBody($newBody);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($newBody, $newRequest->getBody());
    }

    /**
     * Возвращает мок для объекта запроса.
     *
     * @param array $additionalHeaders
     *
     * @return \Bitrix\Main\HttpRequest
     */
    protected function createRequestMock(array $additionalMethods = [])
    {
        $defaultMethods = [
            'getRequestUri' => '/',
            'getRequestMethod' => 'GET',
        ];
        $allMethods = array_merge($defaultMethods, $additionalMethods);

        $bxRequest = $this->getMockBuilder('Bitrix\Main\HttpRequest')
            ->setMethods(array_keys($allMethods))
            ->getMock();
        foreach ($allMethods as $methodName => $methodValue) {
            $bxRequest->method($methodName)->will($this->returnValue($methodValue));
        }

        return $bxRequest;
    }

    /**
     * Возвращает мок для объекта сервера.
     *
     * @param array $additionalHeaders
     *
     * @return \Bitrix\Main\Server
     */
    protected function createServerMock(array $additionalValues = [])
    {
        $defaultValues = [
            'SCRIPT_URL' => '/',
            'SCRIPT_URI' => 'http://localhost/',
            'HTTP_HOST' => 'localhost',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36',
            'HTTP_DNT' => '1',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, br',
            'HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'HTTP_COOKIE' => 'BITRIX_SM_SOUND_LOGIN_PLAYED=Y; BITRIX_SM_LOGIN=admin; PHPSESSID=212b96abece5708ea3a5e67122ad3a00; BITRIX_SM_LAST_SETTINGS=',
            'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
            'SERVER_NAME' => 'localhost',
            'SERVER_ADDR' => '::1',
            'SERVER_PORT' => '80',
            'REMOTE_ADDR' => '::1',
            'DOCUMENT_ROOT' => '/var/www/html',
            'REQUEST_SCHEME' => 'http',
            'CONTEXT_PREFIX' => '',
            'CONTEXT_DOCUMENT_ROOT' => '/var/www/html',
            'SERVER_ADMIN' => 'webmaster@localhost',
            'SCRIPT_FILENAME' => '/var/www/html/index.php',
            'REMOTE_PORT' => '47548',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => '',
            'REQUEST_URI' => '/',
            'SCRIPT_NAME' => '/index.php',
            'PHP_SELF' => '/index.php',
            'REQUEST_TIME_FLOAT' => 1542606755.926,
            'REQUEST_TIME' => 1542606755,
        ];
        $allValues = array_merge($defaultValues, $additionalValues);

        $server = $this->getMockBuilder('Bitrix\Main\Server')
            ->setMethods(['toArray', 'get'])
            ->getMock();
        $server->method('toArray')->will($this->returnValue($allValues));
        $server->method('get')->will($this->returnCallback(function ($params) use ($allValues) {
            return isset($allValues[$param]) ? $allValues[$param] : null;
        }));

        return $server;
    }
}
