docker compose up --build


docker compose exec -u root db bash

docker exec -u root -it gwmariadb git config --global --add safe.directory /var/www/html
docker exec -u root -it gwmariadb composer install

Teste de conexão:

    curl -X GET http://localhost:8080/ -H "X-Token: 123"


    curl -X POST http://localhost:8080/ -H "X-Token: 123" \
     -H "Content-Type: application/json" \
     -d '{"action":"list_databases"}'


curl -X GET http://localhost:8080/db/users -H "X-Token: 123"