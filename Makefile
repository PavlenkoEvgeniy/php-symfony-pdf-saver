restart:
	docker compose -f docker/docker-compose.yml up -d

stop:
	docker compose -f docker/docker-compose.yml down

lint: cs-fix phpstan peck

cs-fix:
	docker exec pdfsaver-php-fpm vendor/bin/php-cs-fixer fix --allow-risky=yes

peck:
	docker exec pdfsaver-php-fpm vendor/bin/peck

phpstan:
	docker exec pdfsaver-php-fpm vendor/bin/phpstan analyse --memory-limit=256M
