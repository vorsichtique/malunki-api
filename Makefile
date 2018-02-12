.PHONY: start
start:
	bash bin/docker_start.sh
	docker-compose up -d

test : unit behat

.PHONY: unit
unit:
	docker-compose exec app vendor/bin/phpunit

.PHONY: behat
behat:
	 docker-compose exec app vendor/bin/behat

cache : cacheclear cachewarmup assets permissions

.PHONY: cacheclear
cacheclear:
	docker-compose exec app bin/console cache:clear --no-warmup

.PHONY: cachewarmup
cachewarmup:
	docker-compose exec app bin/console cache:warmup

# hack to fix container permissions issue
.PHONY: permissions
permissions:
	docker-compose exec app chown -R www-data var

.PHONY: assets
assets:
	docker-compose exec app bin/console assets:install

.PHONY: schema
schema:
	docker-compose exec app bin/console doctrine:schema:update --force

.PHONY: db-dump
db-dump:
	docker-compose exec db /bin/bash /usr/local/bin/db-dump

.PHONY: dev
dev:
	docker-compose -f docker-compose.dev.yml up -d --build
