help:
	@echo "+------------------------------------------------------------------------------+"
	@echo "|                         List of available commands:                          |"
	@echo "+------------------------------------------------------------------------------+"
	@echo "1. init ............................................ Initialize the application."
	@echo "2. build .................................... Build and start Docker containers."
	@echo "3. restart .......................................... Restart Docker containers."
	@echo "4. stop ................................................ Stop Docker containers."
	@echo "5. lint ................. Run code quality checks (PHP CS Fixer, PHPStan, Peck)."
	@echo "6. security-check ........................... Run security audit using Composer."
	@echo "7. cs-check ............................... Check code style using PHP CS Fixer."
	@echo "8. cs-fix ............................ Fix code style issues using PHP CS Fixer."
	@echo "9. peck ............................................ Run Peck for check grammar."
	@echo "10. phpstan ................................... Run PHPStan for static analysis."
	@echo "11. test .................................................... Run PHPUnit tests."
	@echo "12. test-coverage ........................ Run PHPUnit tests with code coverage."
	@echo "+------------------------------------------------------------------------------+"

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

security-check:
	docker exec pdfsaver-php-fpm composer audit

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
