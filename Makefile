.PHONY:fixtures etl csfixer test

fixtures:
	bin/console hautelook:fixtures:load

etl:
	bin/console app:etl:article

csfixer:
	php php-cs-fixer fix src/
	php php-cs-fixer fix tests/

test:
	./bin/phpunit

localup:
	php -S 127.0.0.1:8000 -t public &
	docker-compose up &