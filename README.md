# Автотесты формы авторизации Stellantis AutoCRM

Тестовое задание для формы входа `https://stellantis.autocrm.ru/`. Тесты написаны на PHP с использованием PHPUnit и php-webdriver.

## Покрытие задания

| Требование | Реализация |
|---|---|
| Неверные логин и пароль | `loginWithInvalidCredentialsShowsGenericError` |
| Пустые поля | `emptyFormShowsValidationError` |
| Маскирование пароля | `passwordIsMasked` |
| Ссылка «Забыли пароль» | `forgotPasswordLinkOpensRecoveryForm` |
| Создание пользователя через БД | `TestUserHelper` и `PdoUserDatabase` |
| Работа без настоящей БД | `TestUserHelperTest` |
| API-запрос создания пользователя | `CreateUserRequest` и `examples/create_user.php` |
| Дополнительные сценарии | `additional-tests.md` |

## Структура проекта

```text
.
├── .github/workflows/tests.yml
├── additional-tests.md
├── examples
│   ├── create_user.php
│   └── valid_login_setup.php
├── src
│   ├── Api/CreateUserRequest.php
│   └── Support
├── tests
│   ├── Functional/LoginFormTest.php
│   └── Unit
├── compose.yaml
├── composer.json
├── composer.lock
└── phpunit.xml.dist
```

## Требования

- PHP 8.2 или новее с расширениями `curl` и `json`;
- Composer;
- Docker либо запущенный Selenium Server.

Headless-режим отключен: перед формой авторизации выполняется JavaScript-проверка браузера. Chromium запускается в виртуальном дисплее Selenium-контейнера.

## Быстрый запуск

```bash
composer install
docker compose up -d selenium
composer test
docker compose down
```

Unit-тесты без браузера:

```bash
composer test:unit
```

Только функциональные тесты:

```bash
composer test:functional
```

Полный прогон: 6 тестов, 20 assertions.

Адрес стенда и Selenium можно переопределить переменными окружения:

```bash
BASE_URL=https://stellantis.autocrm.ru \
SELENIUM_URL=http://localhost:4444/wd/hub \
composer test:functional
```

## Хелпер пользователя

`TestUserHelper` создает пользователя перед тестом и удаляет его после выполнения. `PdoUserDatabase` содержит пример параметризованного `INSERT`. В unit-тесте вместо настоящей БД используется простая реализация `UserDatabase` в памяти.

Пример использования с PDO находится в `examples/valid_login_setup.php`. Параметры подключения передаются через переменные окружения.

## Пример API

```bash
API_BASE_URL=https://stellantis.autocrm.ru \
API_CREATE_USER_PATH=/api/v1/users \
API_TOKEN=replace_with_test_token \
php examples/create_user.php
```

Путь API, токен и адрес сервиса задаются через переменные окружения. Значения нужно заменить на данные тестового стенда.
