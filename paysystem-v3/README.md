# PaySystem v2.0 — чистое ООП + Front Controller

Полностью объектно-ориентированная реализация платёжной системы с единой точкой входа,
маршрутизацией, middleware, PHP-шаблонизатором (Bootstrap 5) и JSON API.

## Требования

- PHP 8.4+ (используются property hooks)
- Composer

## Установка и запуск

```bash
composer install
cp .env.example .env            # отредактируй JWT_SECRET
php -S localhost:8000 -t public/
```

Открой `http://localhost:8000/` — увидишь форму входа.

## Тесты

Из корня проекта (`paysystem-v3/`):

```bash
composer install            # подтянуть phpstan и symfony/http-foundation ^7.4
composer dump-autoload      # на случай если ещё не сделано
vendor/bin/phpunit          # или: composer test
```
