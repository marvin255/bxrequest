<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$moduleLibFolder = dirname(__DIR__) . '/marvin255.bxrequest/lib';

spl_autoload_register(function ($class) use ($moduleLibFolder) {
    $class = trim($class, '\\ ');
    if (strpos($class, 'Marvin255\\Bxrequest') === 0) {
        $arClass = explode('\\', $class);
        $name = strtolower(end($arClass));
        $file = "{$moduleLibFolder}/{$name}.php";
        if (file_exists($file)) {
            require $file;
        }
    }
}, true, true);
