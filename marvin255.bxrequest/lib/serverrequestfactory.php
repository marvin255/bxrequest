<?php

namespace Marvin255\Bxrequest;

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
     * Выбрасывает событие для создания нового объекта запроса. Если объект не был
     * задан, то создает объект \Marvin255\Bxrequest\ServerRequest и в качестве
     * параметров передает объекты из стандартного Application::getInstance.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     *
     * @throws \RuntimeException
     */
    public function createRequest()
    {
        $event = new Event('marvin255.bxrequest', 'createRequest', []);
        $event->send();

        $request = $event->getParameter('request');
        if (empty($request)) {
            $app = Application::getInstance();
            $request = new ServerRequest(
                $app->getContext()->getRequest(),
                $app->getContext()->getServer()
            );
        } elseif (!($request instanceof ServerRequestInterface)) {
            throw new RuntimeException(
                "Request from event must implements \Psr\Http\Message\ServerRequestInterface"
            );
        }

        return $request;
    }
}
