include .env

.PHONY: help
help:
	@echo Opciones:
	@echo -------------------
	@echo start / stop / restart
	@echo workspace
	@echo composer-install
	@echo db-shell
	@echo logs
	@echo stats
	@echo clean
	@echo prune
	@echo -------------------

.PHONY: start
start:
	@docker-compose up -d --remove-orphans

.PHONY: stop
stop:
	@docker-compose stop

.PHONY: restart
restart: stop start

.PHONY: workspace
workspace:
	@docker-compose exec php /bin/bash

.PHONY: composer-install
composer-install:
	@docker-compose exec php composer install

.PHONY: db-shell
db-shell:
	@docker-compose exec db mysql -h 127.0.0.1 -P${DB_PORT} -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}

.PHONY: logs
logs:
	@docker-compose logs

.PHONY: stats
stats:
	@docker stats

.PHONY: clean
clean:
	@docker-compose down -v --rmi all --remove-orphans

.PHONY: prune
prune:
	@docker network prune
