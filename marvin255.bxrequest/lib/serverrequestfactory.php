<?php

namespace Marvin255\Bxrequest;

use Marvin255\Bxrequest\bitrix\HttpRequest;
use Psr\Http\Message\ServerRequestInterface;
use Bitrix\Main\Event;
use Bitrix\Main\Application;
use RuntimeException;

/**
 * Фабрика, которая создает объект запроса длясервера.
 */
class ServerRequestFactory
{
    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected static $instance;

    /**
     * Возвращает объект запроса, если он существует, если не существует, то
     * создает новый и запоминает.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     *
     * @throws \RuntimeException
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = self::createRequest();
        }

        return self::$instance;
    }

    /**
     * Выбрасывает событие для создания нового объекта запроса. Если объект не был
     * задан, то создает объект \Marvin255\Bxrequest\ServerRequest и в качестве
     * параметров передает объекты из стандартного Application::getInstance.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     *
     * @throws \RuntimeException
     */
    public static function createRequest()
    {
        $event = new Event('marvin255.bxrequest', 'createRequest', []);
        $event->send();

        $request = $event->getParameter('request');
        if (empty($request)) {
            $request = self::createRequestDefault();
        } elseif (!($request instanceof ServerRequestInterface)) {
            throw new RuntimeException(
                "Request from event must implements \Psr\Http\Message\ServerRequestInterface"
            );
        }

        return $request;
    }

    /**
     * Создает объект запроса по умолчанию.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected static function createRequestDefault()
    {
        $app = Application::getInstance();
        $server = $app->getContext()->getServer();
        $request = $app->getContext()->getRequest();

        //хак для старых битриксов
        if (!method_exists($request, 'getHeaders')) {
            $request = new HttpRequest($server, $_GET, $_POST, $_FILES, $_COOKIE);
        }

        return new ServerRequest($request, $server);
    }
}
