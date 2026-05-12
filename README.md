Construindo imagem:

    docker build --no-cache -t gwmariadb .
    docker compose up
    cp .env.example .env

Teste de conexão:

    curl -X GET http://localhost:8080/ -H "X-Token: 123"
    curl -X GET http://localhost:8080/ -H "X-Token: 123tokenErrado"


    curl -X POST http://localhost:8080/ -H "X-Token: 123" \
     -H "Content-Type: application/json" \
     -d '{"action":"list_databases"}'


curl -X GET http://localhost:8080/db/users -H "X-Token: 123"