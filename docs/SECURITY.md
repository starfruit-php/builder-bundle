# SECURITY

## NelmioSecurityBundle

Follow [document](https://symfony.com/bundles/NelmioSecurityBundle/current/index.html)

## Custom

Custom config, see [default config](../config/pimcore/starfruit_builder.yaml):

```bash
starfruit_builder:
    ...
    security:
        response:
            remove_headers:
                - 'x-powered-by',
                - 'server'
            custom_hsts_value: 'max-age=7776000'
            custom_csp_value:
```
