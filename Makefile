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