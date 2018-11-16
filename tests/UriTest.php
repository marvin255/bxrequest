<?php

namespace Marvin255\Bxrequest\tests;

use Marvin255\Bxrequest\Uri;
use InvalidArgumentException;

/**
 * Тест для объекта, который инкапсулирует работу с uri.
 */
class UriTest extends BaseTestCase
{
    /**
     * Проверяет, что объект выбрасывает исключение, если указан невалидный uri.
     */
    public function testConstructorParseException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Uri('http://:80');
    }

    /**
     * Проверяет, что объект возвращает scheme часть uri.
     */
    public function testGetScheme()
    {
        $this->assertSame(
            'https',
            (new Uri('https://test.ru/test'))->getScheme(),
            'Uri class must retun a scheme'
        );

        $this->assertSame(
            'http',
            (new Uri('hTTp://test.ru/test'))->getScheme(),
            'Uri class must normalize scheme to lowercase'
        );

        $this->assertSame(
            '',
            (new Uri('test.ru/test'))->getScheme(),
            'Uri class must an empty string if no scheme presented'
        );
    }

    /**
     * Проверяет, что объект возвращает authority часть uri.
     */
    public function testGetAuthority()
    {
        $this->assertSame(
            'test.ru',
            (new Uri('https://test.ru/test'))->getAuthority(),
            'Uri class must return host in authority'
        );

        $this->assertSame(
            'test.ru:8080',
            (new Uri('https://test.ru:8080/test'))->getAuthority(),
            'Uri class must return host and port in authority'
        );

        $this->assertSame(
            'user:password@test.ru:8080',
            (new Uri('https://user:password@test.ru:8080/test'))->getAuthority(),
            'Uri class must return host, port and user info in authority'
        );

        $this->assertSame(
            'user:password@test.ru',
            (new Uri('https://user:password@test.ru/test'))->getAuthority(),
            'Uri class must return username and password'
        );

        $this->assertSame(
            'test.ru',
            (new Uri('https://test.ru:443/test'))->getAuthority(),
            'Uri class must not return default port in authority'
        );

        $this->assertSame(
            '',
            (new Uri('/test'))->getAuthority(),
            'Uri class must an empty string if no authority presented'
        );
    }

    /**
     * Проверяет, что объект возвращает userinfo часть uri.
     */
    public function testGetUserInfo()
    {
        $this->assertSame(
            'user',
            (new Uri('https://user@test.ru/test'))->getUserInfo(),
            'Uri class must return username'
        );

        $this->assertSame(
            'user:password',
            (new Uri('https://user:password@test.ru/test'))->getUserInfo(),
            'Uri class must return username and password'
        );

        $this->assertSame(
            '',
            (new Uri('https://test.ru/test'))->getUserInfo(),
            'Uri class must return an empty string if no user info presented'
        );
    }

    /**
     * Проверяет, что объект возвращает host часть uri.
     */
    public function testGetHost()
    {
        $this->assertSame(
            'test.ru',
            (new Uri('https://user@test.ru/test'))->getHost(),
            'Uri class must return host'
        );

        $this->assertSame(
            'test.ru',
            (new Uri('https://user:password@tESt.ru/test'))->getHost(),
            'Uri class must normalize host to lowercase'
        );

        $this->assertSame(
            '',
            (new Uri('/test'))->getHost(),
            'Uri class must return an empty string if no host presented'
        );
    }

    /**
     * Проверяет, что объект возвращает port часть uri.
     */
    public function testGetPort()
    {
        $this->assertSame(
            8080,
            (new Uri('https://test.ru:8080/test'))->getPort(),
            'Uri class must return port'
        );

        $this->assertSame(
            null,
            (new Uri('https://user@test.ru/test'))->getPort(),
            'Uri class must return null if no port presented'
        );

        $this->assertSame(
            null,
            (new Uri('https://test.ru:443/test'))->getPort(),
            'Uri class must return null if default port presented'
        );
    }

    /**
     * Проверяет, что объект возвращает path часть uri.
     */
    public function testGetPath()
    {
        $this->assertSame(
            '',
            (new Uri('https://test.ru'))->getPath(),
            'Uri class must return empty string if no path presented'
        );

        $this->assertSame(
            '/test',
            (new Uri('https://user@test.ru/test'))->getPath(),
            'Uri class must return path'
        );

        $this->assertSame(
            '/',
            (new Uri('https://test.ru:443/'))->getPath(),
            'Uri class must return path with slash'
        );
    }

    /**
     * Проверяет, что объект возвращает query часть uri.
     */
    public function testGetQuery()
    {
        $this->assertSame(
            '',
            (new Uri('https://test.ru'))->getQuery(),
            'Uri class must return empty string if no query presented'
        );

        $this->assertSame(
            'a=10&b=12',
            (new Uri('https://user@test.ru/test?a=10&b=12'))->getQuery(),
            'Uri class must return query'
        );

        $this->assertSame(
            '',
            (new Uri('https://test.ru:443/?'))->getQuery(),
            'Uri class must return empty string if no query presented after \?'
        );
    }

    /**
     * Проверяет, что объект возвращает fragment часть uri.
     */
    public function testGetFragment()
    {
        $this->assertSame(
            '',
            (new Uri('https://test.ru'))->getFragment(),
            'Uri class must return empty string if no fragment presented'
        );

        $this->assertSame(
            'fragment',
            (new Uri('https://user@test.ru/test#fragment'))->getFragment(),
            'Uri class must return fragment'
        );

        $this->assertSame(
            '',
            (new Uri('https://test.ru:443/#'))->getFragment(),
            'Uri class must return empty string if no fragment presented after \#'
        );
    }

    /**
     * Проверяет, что объект создает новую копию с указанием новой схемы.
     */
    public function testWithScheme()
    {
        $uriString = '//user:pass@test.ru:8080/test/test?a=1&b=2#fragment';

        $uri = new Uri("http:{$uriString}");
        $newSchemeUri = $uri->withScheme('https');

        $this->assertNotSame($uri, $newSchemeUri);
        $this->assertSame("https:{$uriString}", (string) $newSchemeUri);
    }

    /**
     * Проверяет, что объект выбрасываетисключение, при попытке использовать
     * неизвестную схему.
     */
    public function testWithSchemeException()
    {
        $uriString = '//user:pass@test.ru:8080/test/test?a=1&b=2#fragment';

        $uri = new Uri('http://test.ru');

        $this->setExpectedException(InvalidArgumentException::class);
        $uri->withScheme('test');
    }

    /**
     * Проверяет, что объект создает новую копию с указанием новых данных пользователя.
     */
    public function testWithUserInfo()
    {
        $uri = new Uri('http://user@test.ru');
        $newUserInfoUri = $uri->withUserInfo('test', 'pass');

        $this->assertNotSame($uri, $newUserInfoUri);
        $this->assertSame('http://test:pass@test.ru', (string) $newUserInfoUri);
    }

    /**
     * Проверяет, что объект создает новую копию с указанием нового хоста.
     */
    public function testWithHost()
    {
        $uri = new Uri('http://user@test.ru');
        $newHostUri = $uri->withHost('new.host');

        $this->assertNotSame($uri, $newHostUri);
        $this->assertSame('http://user@new.host', (string) $newHostUri);
    }

    /**
     * Проверяет, что объект создает новую копию с указанием нового порта.
     */
    public function testWithPort()
    {
        $uri = new Uri('http://test.ru:100');
        $newPortUri = $uri->withPort(200);

        $this->assertNotSame($uri, $newPortUri);
        $this->assertSame('http://test.ru:200', (string) $newPortUri);
    }

    /**
     * Проверяет, что объект выбрасывает исключение, если указан неврный порт.
     */
    public function testWithPortException()
    {
        $uri = new Uri('http://test.ru:100');

        $this->setExpectedException(InvalidArgumentException::class);
        $uri->withPort('test');
    }

    /**
     * Проверяет, что объект создает новую копию с указанием нового пути.
     */
    public function testWithPath()
    {
        $uri = new Uri('http://test.ru:100/path');
        $newPathUri = $uri->withPath('test');

        $this->assertNotSame($uri, $newPathUri);
        $this->assertSame('http://test.ru:100/test', (string) $newPathUri);
    }

    /**
     * Проверяет, что объект создает новую копию с указанием новых параметров.
     */
    public function testWithQuery()
    {
        $uri = new Uri('http://test.ru?a=10');
        $newQueryUri = $uri->withQuery('test=20');

        $this->assertNotSame($uri, $newQueryUri);
        $this->assertSame('http://test.ru?test=20', (string) $newQueryUri);
    }

    /**
     * Проверяет, что объект создает новую копию с указанием нового фрагмента.
     */
    public function testWithFragment()
    {
        $uri = new Uri('http://test.ru#fragment');
        $newFragmentUri = $uri->withFragment('test');

        $this->assertNotSame($uri, $newFragmentUri);
        $this->assertSame('http://test.ru#test', (string) $newFragmentUri);
    }

    /**
     * Проверяет, что объект приводится к строке.
     */
    public function testToString()
    {
        $this->assertSame(
            'https://user:pass@test.ru:8080/test/test?a=1&b=2#fragment',
            (string) (new Uri('https://user:pass@test.ru:8080/test/test?a=1&b=2#fragment')),
            'Uri class must use __toString magic'
        );
    }
}
