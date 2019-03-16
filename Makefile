.PHONY:fixtures etl csfixer

fixtures:
	bin/console hautelook:fixtures:load

etl:
	bin/console app:etl:article

csfixer:
	php php-cs-fixer fix src/