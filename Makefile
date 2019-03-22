.PHONY:fixtures etl csfixer test test-v testfonc

fixtures:
	bin/console hautelook:fixtures:load --no-debug

etl:
	bin/console app:etl:article --no-debug -vvv

csfixer:
	php php-cs-fixer fix src/
	php php-cs-fixer fix tests/

test-v:
	PANTHER_NO_HEADLESS=1 ./vendor/bin/simple-phpunit

test:
	./vendor/bin/simple-phpunit

testfonc:
	./vendor/bin/simple-phpunit ./tests/Model

localup:
	php -S 127.0.0.1:8000 -t public &
	docker-compose up &