Construindo imagem:

    docker build --no-cache -t gwmariadb .
    docker compose up
    cp .env.example .env
    docker exec -it gwmariadb composer install

Teste de conexão:

    curl -X GET http://localhost:8080/ -H "X-Token: 123"
    curl -X GET http://localhost:8080/ -H "X-Token: 123tokenErrado"

Requisições existentes:

    curl -X POST http://localhost:8080/ -H "X-Token: 123" \
        -H "Content-Type: application/json" \
        -d '{"action":"listar_databases"}'

    curl -X POST http://localhost:8080/ -H "X-Token: 123" \
        -H "Content-Type: application/json" \
        -d '{"action":"listar_usuarios"}'

Novas requisições:

    curl -X POST http://localhost:8080/ -H "X-Token: 123" \
        -H "Content-Type: application/json" \
        -d '{"action":"database_existe", "nome":"nome_database"}'

    curl -X POST http://localhost:8080/ -H "X-Token: 123" \
        -H "Content-Type: application/json" \
        -d '{"action":"usuario_existe", "nome":"nome_usuario"}'