# Laravel Products API

Тестовое задание: HTTP endpoint для поиска товаров с фильтрами, сортировкой, пагинацией и Swagger-документацией.

## Запуск через Docker Compose

```bash
docker compose up
```

После старта приложение доступно на `http://127.0.0.1:8000`.

Compose использует SQLite, устанавливает зависимости, создает `.env`, запускает миграции, генерирует Swagger и поднимает `php artisan serve`.

## Make-команды

```bash
make setup
make serve
make swagger
```

- `make setup` - `composer install`, подготовка `.env`, миграции и сидеры.
- `make serve` - запуск Laravel через `php artisan serve`.
- `make swagger` - генерация Swagger/OpenAPI документации.

## API

```http
GET /api/products
```

Query-параметры:

- `q` - поиск по подстроке в `name`
- `price_from`, `price_to` - фильтр по цене
- `category_id` - фильтр по категории
- `in_stock` - `true` или `false`
- `rating_from` - минимальный рейтинг
- `sort` - `price_asc`, `price_desc`, `rating_desc`, `newest`
- `page`, `per_page` - пагинация, максимум `per_page=100`

Пример:

```bash
curl "http://127.0.0.1:8000/api/products?q=phone&price_from=100&in_stock=true&sort=price_asc&per_page=10"
```

## Swagger

- UI: `http://127.0.0.1:8000/api/documentation`
- JSON: `http://127.0.0.1:8000/docs`

## Проверка

```bash
docker compose run --rm app php artisan test
```
