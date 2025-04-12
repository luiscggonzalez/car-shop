.PHONY: start
start:
	./vendor/bin/sail up -d

.PHONY: stop
stop:
	./vendor/bin/sail stop

.PHONY: down
down:
	./vendor/bin/sail down

.PHONY: migrate
migrate:
	./vendor/bin/sail php artisan migrate

.PHONY: test
test:
	./vendor/bin/sail test

.PHONY:  generate-docs
generate-docs:
	./vendor/bin/sail php artisan l5-swagger:generate

.PHONY:  static-analysis
static-analysis:
	./vendor/bin/sail php vendor/bin/phpstan analyse

.PHONY:  clean-cache
clean-cache:
	./vendor/bin/sail php artisan cache:clear
