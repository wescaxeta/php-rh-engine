.PHONY: help build install test coverage analyse cs cs-fix ci shell

DC = docker compose run --rm php

help: ## Lista os comandos disponíveis
	@grep -E '^[a-z-]+:.*## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*## "}; {printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'

build: ## Constrói a imagem Docker
	docker compose build

install: ## Instala as dependências
	$(DC) composer install

test: ## Roda os testes
	$(DC) composer test

coverage: ## Roda os testes com cobertura
	$(DC) composer test:coverage

analyse: ## Análise estática (PHPStan)
	$(DC) composer analyse

cs: ## Verifica o estilo de código
	$(DC) composer cs

cs-fix: ## Corrige o estilo de código
	$(DC) composer cs:fix

ci: ## Pipeline completo (estilo + análise + testes)
	$(DC) composer ci

shell: ## Abre um shell no container
	$(DC) sh
