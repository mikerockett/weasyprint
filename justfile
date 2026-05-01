dc := "docker compose run --rm wp"

build:
    docker compose build

test *args:
    {{dc}} vendor/bin/pest --profile {{args}}

lint *args:
    {{dc}} vendor/bin/mago lint {{args}}

analyze *args:
    {{dc}} vendor/bin/mago analyze {{args}}

fix:
    {{dc}} vendor/bin/php-cs-fixer fix --allow-risky=yes

composer *args:
    {{dc}} composer {{args}}

shell:
    {{dc}} sh
