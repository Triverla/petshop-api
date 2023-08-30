up:
	docker-compose \
		-f docker-compose.yml \
		up -d --build --remove-orphans

down:
	docker-compose -f docker-compose.yml stop

composer:
	docker exec -t petshop bash -c 'COMPOSER_MEMORY_LIMIT=-1 composer install'

migration:
	docker exec -t petshop bash -c 'php artisan migrate'

laravel:
	docker exec -it petshop bash

db:
	docker exec -it Database bash
