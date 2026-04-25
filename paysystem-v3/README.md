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

## Архитектура

```
paysystem-v2/
├── public/index.php           — Front Controller (единственная точка входа)
├── bootstrap.php              — сборка контейнера (сервисы, контроллеры, роуты)
├── src/
│   ├── Application.php        — стартует pipeline (middlewares → router → response)
│   ├── Request.php, Response.php
│   ├── Router.php, Route.php  — {name} и {name:regex} параметры
│   ├── Controller/            — AbstractController, Payment, User, Auth
│   ├── Middleware/            — Auth (JWT cookie + API/HTML split), Logging
│   ├── Service/               — бизнес-логика (Payment, User, Authentication, Jwt)
│   ├── Repository/            — JSON-хранилище + интерфейсы
│   ├── Factory/               — PaymentMethodFactory (Stripe/Mollie/Flutterwave)
│   ├── Processor/, Strategy/  — обработка платежей и комиссии
│   ├── DTO/, Entity/, Enum/
│   ├── Exception/             — ValidationException, NotFoundException и ExceptionHandler
│   └── View/TemplateEngine.php — extract + ob_start, renderWithLayout
├── templates/                 — PHP-шаблоны (Этап 3 заменит на Twig)
│   ├── layout.php             — Bootstrap 5 CDN
│   ├── components/            — navbar, flash
│   ├── auth/                  — login, register
│   └── payments/              — list, create
└── data/                      — users.json, payments.json
```

## HTTP контракт

### HTML (браузер)

| Маршрут | Описание |
|---------|----------|
| `GET /login` / `GET /register`         | формы |
| `POST /auth/login` / `POST /auth/register` | обработка форм |
| `GET /logout`                          | выход (чистит cookie) |
| `GET /payments[?status=pending]`       | список платежей пользователя |
| `GET /payments/create`                 | форма создания платежа |
| `POST /payments/store`                 | создание платежа |

### JSON API

| Маршрут | Описание |
|---------|----------|
| `POST /api/payments`                     | создать платёж |
| `GET /api/payments`                      | список платежей пользователя |
| `GET /api/payments/status/{status}`      | фильтр по статусу |
| `GET /api/payments/{id}`                 | один платёж по UUID |
| `POST /api/payments/{id}/refund`         | возврат |
| `POST /users/register` / `GET /users/{id}` | users API |

## Авторизация

- `POST /auth/login` кладёт JWT в HttpOnly-cookie `access_token`.
- `AuthMiddleware` декодирует cookie: HTML-запрос без токена → 302 `/login`, API → 401 JSON.
- Публичные маршруты: `/login`, `/register`, `/auth/login`, `/auth/register`, `/users/register`.

## Обработка ошибок

`Application::run()` оборачивает `Router::dispatch()` в try/catch.
`ExceptionHandler` мапит исключения → HTTP-статусы:

| Exception | Status |
|-----------|--------|
| `ValidationException` | 422 |
| `NotFoundException`   | 404 |
| `AuthenticationException` | 401 |
| остальные             | 500 |

## Smoke-тест

```bash
# 1. старт сервера
php -S localhost:8000 -t public/

# 2. браузер: /register → /login → /payments → /payments/create

# 3. API
curl -c cookie.txt -X POST http://localhost:8000/auth/login \
  --data-urlencode "email=anton1999@gmail.com" --data-urlencode "password=anton1999"

curl -b cookie.txt http://localhost:8000/api/payments
curl -b cookie.txt http://localhost:8000/api/payments/status/pending
```

## Что дальше (Этап 3 Middle)

V2 построен так, чтобы каждый компонент имел прямой аналог в Symfony:

| V2                          | Этап 3 (Symfony)                  |
|-----------------------------|-----------------------------------|
| `Request` / `Response`      | `symfony/http-foundation`         |
| `Router` + `Route`          | `symfony/routing` + `#[Route]`    |
| `JsonStorage` репозитории   | Doctrine DBAL → ORM               |
| Ручная сборка в bootstrap   | `DependencyInjection` + services.yaml |
| `TemplateEngine`            | Twig                              |
| `ExceptionHandler`          | `ExceptionListener`               |

Код намеренно ручной, чтобы переход на Symfony был осознанным.
