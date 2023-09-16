## Snapp Bank


## Setup

you must install docker and docker-compose. after, clone project. then:



create .env file by copying from .env.example

```bash
cp .env.example .env
```

after, create and modify env file you must build service and containers

```bash
docker compose up -d
```

after, that all services pulled and started migrate database

```bash
docker exec -it snapp-bank-php composer install
```

then generate key

```bash
docker exec -it snapp-bank-php php /var/www/html/artisan key:generate
```

then migrate database

```bash
docker exec -it snapp-bank-php php /var/www/html/artisan migrate
```


## Test
```bash
docker exec -it snapp-bank-php php /var/www/html/artisan test
```

