DC=docker compose
APP=$(DC) run --rm app

.PHONY: help serve setup swagger test lint down

help:
	@echo "Available commands:"
	@echo "  make setup    - composer install, migrate and seed SQLite database"
	@echo "  make serve    - start Laravel serve on http://127.0.0.1:8000"
	@echo "  make swagger  - generate Swagger/OpenAPI documentation"
	@echo "  make test     - run Laravel tests"
	@echo "  make lint     - run Laravel Pint"
	@echo "  make down     - stop docker compose services"

setup:
	$(APP) sh -lc "composer install && if [ ! -f .env ]; then cp .env.example .env; fi && if ! grep -q '^APP_KEY=base64:' .env; then php artisan key:generate --force; fi && touch database/database.sqlite && php artisan migrate --force && php artisan db:seed --force"

serve:
	$(DC) up app

swagger:
	$(APP) php artisan l5-swagger:generate

test:
	$(APP) php artisan test

lint:
	$(APP) ./vendor/bin/pint

down:
	$(DC) down

sidding:
	$(APP) php artisan migrate:fresh --seed

run:
	@echo 'Заходите в свагер ( добавте в путь /api/documentation ) и посмотрит как работает АПИ ендпоинт'
	@echo 'после запуска > http://0.0.0.0:8000/api/documentation'
	@sleep 3
	docker compose up
