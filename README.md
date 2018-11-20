# Bxrequest

[![Latest Stable Version](https://poser.pugx.org/marvin255/bxrequest/v/stable.png)](https://packagist.org/packages/marvin255/bxrequest)
[![License](https://poser.pugx.org/marvin255/bxrequest/license.svg)](https://packagist.org/packages/marvin255/bxrequest)
[![Build Status](https://travis-ci.org/marvin255/bxrequest.svg?branch=master)](https://travis-ci.org/marvin255/bxrequest)

PSR-7 совместимые http сообщения для 1С-Битрикс.



## Установка

**С помощью [Composer](https://getcomposer.org/doc/00-intro.md)**

1. Добавьте в ваш composer.json в раздел `require`:

    ```javascript
    "require": {
        "marvin255/bxrequest": "~1.0"
    }
    ```

2. Если требуется автоматическое обновление модуля через composer, то добавьте в раздел `scripts`:

    ```javascript
    "scripts": {
        "post-install-cmd": [
            "\\marvin255\\bxrequest\\installer\\Composer::injectModule"
        ],
        "post-update-cmd": [
            "\\marvin255\\bxrequest\\installer\\Composer::injectModule"
        ]
    }
    ```

3. Выполните в консоли внутри вашего проекта:

    ```
    composer update
    ```

4. Если пункт 2 не выполнен, то скопируйте папку `vendor/marvin255/bxrequest/marvin255.bxrequest` в папку `local/modules` вашего проекта. А папку `vendor/psr/http-message/src` в папку `local/modules/marvin255.bxrequest/psr_http_message`.

5. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".



## Получение инстанса

Для того, чтобы получить инстанс http запроса, воспользуйтесь фабрикой:

```php
use Bitrix\Main\Loader;
use Marvin255\Bxrequest\ServerRequestFactory;

Loader::includeModule('marvin255.bxrequest');
$request = ServerRequestFactory::instance();
```
