# mini_framework

Простой процедурный MVC PHP5 фреймворк

# Установить

Можно установить mini_framework двумя способами:

## Через composer

Скачайте/установите [composer](https://getcomposer.org/doc/00-intro.md), создайте файл `composer.json`, добавьте в `composer.json` код:

```json
{
    "require": {
        "volter/mini_framework": "1.2.2"
    }
}
```

И запустите комманду `composer install`. После установки подключите файл `vendor/autoload.php`. Теперь mini_framework готов к использованию.

## Скачать

Просто скачайте репозиторий, разместите на веб сервере и подключите `src/app.php` файл:

```php
require 'src/app.php';
```

# Документация

Документацию можете найти в [Wiki](https://github.com/Volter9/mini_framework/wiki).