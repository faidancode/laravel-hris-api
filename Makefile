# =========================
# ENV
# =========================
include .env
export

APP_NAME=laravel-hris-api
APP_ENV=local

# =========================
# DOCKER
# =========================
.PHONY: docker-up
docker-up:
	docker-compose up -d --build

.PHONY: docker-down
docker-down:
	docker-compose down

.PHONY: docker-infra
docker-infra:
	docker-compose up -d postgres

.PHONY: docker-infra-stop
docker-infra-stop:
	docker-compose stop postgres

.PHONY: docker-logs
docker-logs:
	docker-compose logs -f

# Menjalankan migrasi di dalam docker
.PHONY: docker-migrate
docker-migrate:
	docker-compose run --rm migrator

# =========================
# MONITORING
# =========================

# Cek status container proyek ini saja
.PHONY: ps
ps:
	docker compose ps

# Cek semua container yang ada di komputer dengan format tabel
.PHONY: docker-ls
docker-ls:
	docker ps -a

	# =========================
# Laravel Makefile
# =========================

# Default PHP & Artisan
PHP=php
ARTISAN=$(PHP) artisan

# =========================
# App
# =========================

up:
	$(ARTISAN) serve

down:
	@echo "Stop server manually (CTRL+C)"

# =========================
# Testing (Pest / PHPUnit)
# =========================

# make test-file file=tests/Unit/Services/UserServiceTest.php

test:
	$(ARTISAN) test

test-unit:
	$(ARTISAN) test tests/Unit

test-feature:
	$(ARTISAN) test tests/Feature

test-filter:
	$(ARTISAN) test --filter=$(filter)

test-file:
	$(ARTISAN) test $(file)

test-verbose:
	$(ARTISAN) test -v

# Example:
# make test-file file=tests/Unit/PositionServiceTest.php
# make test-filter filter=paginate

# =========================
# Cache & Config
# =========================

cache-clear:
	$(ARTISAN) cache:clear

config-clear:
	$(ARTISAN) config:clear

route-clear:
	$(ARTISAN) route:clear

view-clear:
	$(ARTISAN) view:clear

optimize-clear:
	$(ARTISAN) optimize:clear

# Clear all (favorite 🔥)
clear-all: cache-clear config-clear route-clear view-clear

# =========================
# Cache Build
# =========================

config-cache:
	$(ARTISAN) config:cache

route-cache:
	$(ARTISAN) route:cache

view-cache:
	$(ARTISAN) view:cache

optimize:
	$(ARTISAN) optimize

# =========================
# Database
# =========================

migrate:
	$(ARTISAN) migrate

migrate-fresh:
	$(ARTISAN) migrate:fresh

migrate-seed:
	$(ARTISAN) migrate --seed

fresh-seed:
	$(ARTISAN) migrate:fresh --seed

seed:
	$(ARTISAN) db:seed

# =========================
# Queue
# =========================

queue-work:
	$(ARTISAN) queue:work

queue-restart:
	$(ARTISAN) queue:restart

# =========================
# Logs
# =========================

logs:
	tail -f storage/logs/laravel.log