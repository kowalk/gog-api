## Setup

1. Clone the repository
2. Run 
```bash
docker compose -f docker-compose.local.yml up -d
docker compose -f docker-compose.local.yml exec phpfpm composer install
docker compose -f docker-compose.local.yml exec phpfpm bin/console doctrine:migrations:migrate
```

Read api documentation from file `api-docs.html` in the root of the project.