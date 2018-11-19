<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$moduleLibFolder = dirname(__DIR__) . '/marvin255.bxrequest/lib';

spl_autoload_register(function ($className) use ($moduleLibFolder) {
    $className = trim($className, '\\ ');
    if (strpos($className, 'Marvin255\\Bxrequest') === 0) {
        $relativeClassName = strtolower(substr($className, 20));
        $arClass = explode('\\', $relativeClassName);
        $name = array_pop($arClass);
        if ($arClass) {
            $path = implode('/', $arClass) . '/';
        } else {
            $path = '';
        }
        $file = "{$moduleLibFolder}/{$path}{$name}.php";
        if (file_exists($file)) {
            require $file;
        }
    }
}, true, true);
