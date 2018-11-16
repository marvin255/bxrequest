<?php

use Bitrix\Main\Loader;

//автозагрузчик для интерфейсов PSR-7
$psrClassesDir = __DIR__ . '/psr_http_message';
Loader::registerAutoLoadClasses('marvin255.bxrequest', [
    'Psr\Http\Message\MessageInterface' => $psrClassesDir . '/MessageInterface.php',
    'Psr\Http\Message\RequestInterface' => $psrClassesDir . '/RequestInterface.php',
    'Psr\Http\Message\ResponseInterface' => $psrClassesDir . '/ResponseInterface.php',
    'Psr\Http\Message\ServerRequestInterface' => $psrClassesDir . '/ServerRequestInterface.php',
    'Psr\Http\Message\StreamInterface' => $psrClassesDir . '/StreamInterface.php',
    'Psr\Http\Message\UploadedFileInterface' => $psrClassesDir . '/UploadedFileInterface.php',
    'Psr\Http\Message\UriInterface' => $psrClassesDir . '/UriInterface.php',
]);
