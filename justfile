dc := "docker compose run --rm wp"

build:
    docker compose build

test *args:
    {{dc}} vendor/bin/pest --profile {{args}}

fix:
    {{dc}} vendor/bin/php-cs-fixer fix --allow-risky=yes

composer *args:
    {{dc}} composer {{args}}

shell:
    {{dc}} sh
