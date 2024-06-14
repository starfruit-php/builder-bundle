# CACHE

Setup cache using Redis

## Setup

1. Install library:

    ```bash
    composer require predis/predis
    ```

2. Update `config/config.yaml` file:

    ```bash
    imports:
        ...
        - { resource: './../vendor/starfruit/builder-bundle/config/framework/cache.yaml', ignore_errors: true }
    ```

3. Update `.env` file with Redis information:

    ```bash
    # example data
    BUILDER_REDIS_DNS_PROVIDER=redis://localhost
    ```
