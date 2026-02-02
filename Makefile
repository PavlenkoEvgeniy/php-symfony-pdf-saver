init: generate-env build composer-install

build:
	docker compose -f docker/docker-compose.yml up -d --build

generate-env:
	bash bin/generate-docker-env.sh

composer-install:
	docker exec pdfsaver-php-fpm composer install

restart:
	docker compose -f docker/docker-compose.yml up -d

stop:
	docker compose -f docker/docker-compose.yml down

lint: cs-fix phpstan peck

cs-check:
	docker exec pdfsaver-php-fpm vendor/bin/php-cs-fixer fix --dry-run --diff --allow-risky=yes

cs-fix:
	docker exec pdfsaver-php-fpm vendor/bin/php-cs-fixer fix --allow-risky=yes

peck:
	docker exec pdfsaver-php-fpm vendor/bin/peck

phpstan:
	docker exec pdfsaver-php-fpm vendor/bin/phpstan analyse --memory-limit=256M

test:
	docker exec pdfsaver-php-fpm vendor/bin/phpunit --colors=always

test-coverage:
	docker exec -e XDEBUG_MODE=coverage pdfsaver-php-fpm vendor/bin/phpunit --colors=always --coverage-text
