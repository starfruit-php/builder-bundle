# MAIL

## Setup

1. Update `config/config.yaml` file:

    ```bash
    imports:
        ...
        - { resource: './../vendor/starfruit/builder-bundle/config/framework/framework.yaml', ignore_errors: true }
    ```

2. Update `.env` file with DSN string:

    ```bash
    BUILDER_MAILER_DSN=smtp://[email_address]:[application_password]@smtp.gmail.com:587
    ```

## Custom config

```bash
starfruit_builder:
    ...
    mail:
        ignore_debug_mode: true # send mail without dev mode skip, default false
```
