build:
	docker compose up -d --build
	docker compose exec php bash -c "export COMPOSER_HOME=/usr/bin && composer install"

start:
	docker compose up -d

stop:
	docker compose down -v --remove-orphans

rebuild:
	docker compose down -v --remove-orphans
	docker compose rm -vsf
	docker compose build
	docker compose up -d
	docker compose exec php bash -c "export COMPOSER_HOME=/usr/bin && composer install"