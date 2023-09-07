up:
	docker-compose \
		-f docker-compose.yml \
		up -d --build --remove-orphans
		docker exec -t petshop bash -c 'php artisan migrate:fresh --seed'

down:
	docker-compose -f docker-compose.yml stop

composer:
	docker exec -t petshop bash -c 'COMPOSER_MEMORY_LIMIT=-1 composer install'

migration:
	docker exec -t petshop bash -c 'php artisan migrate:fresh --seed'

laravel:
	docker exec -it petshop bash

db:
	docker exec -it Database bash

phpinsights:
	docker exec -it petshop bash -c "php artisan insights"

phpstan:
	docker exec -it petshop bash -c "./vendor/bin/phpstan analyse --memory-limit=2G"

idehelper:
	docker exec -it petshop bash -c "php artisan ide-helper:generate"

swagger:
	docker exec -it petshop bash -c "php artisan l5-swagger:generate"

test:
	docker exec -it petshop bash -c "php artisan test"

env:
	cp .env.example .env
