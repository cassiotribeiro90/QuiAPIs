## Multi API - Yii2 + Docker

Projeto com 3 APIs (Lojista, App e Gestor) em um único Yii2, rodando em Docker.

## Pré-requisitos

Docker Desktop instalado.

## Arquivos necessários

- docker-compose.yml
- docker/php/Dockerfile
- docker/nginx/conf.d/api.conf

## Como iniciar

Abra o PowerShell e execute:

cd pasta_do_projeto ex C:\users\user\projetos\apis
docker-compose up -d

## Acessos

- API Lojista: http://localhost:8001
- API App: http://localhost:8002
- API Gestor: http://localhost:8003
- MySQL: localhost:3306 (usuário: app_user, senha: app123, banco: apis_db)

## Comandos úteis

- Parar containers: docker-compose down
- Ver logs: docker-compose logs -f
- Acessar container PHP: docker-compose exec php bash

## Estrutura

A estrutura do docker está exemplificada em api.zip com as configurações para subir ambiente docker

