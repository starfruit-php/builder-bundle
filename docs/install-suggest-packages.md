Suggest Packages Installation
<!-- [TOC] -->

# SEO

1. On your Pimcore 11 root project:
```bash
composer require dachcom-digital/seo
```

2. Update `config/bundles.php` file:
```bash
return [
    ....
    SeoBundle\SeoBundle::class => ['all' => true],
];
```

3. Run install command:
```bash
./bin/console pimcore:bundle:install SeoBundle
```

If there are errors, try running the uninstall command first
```bash
./bin/console pimcore:bundle:uninstall SeoBundle
```

4. Create a new thumbnail for SEO image (ex: **socialThumb**).

5. Create `config/seo.yaml` file with above thumbnail:

```bash
seo:
    meta_data_configuration:
        meta_data_provider:
            auto_detect_documents: true
        meta_data_integrator:
            documents:
                enabled: true
                hide_pimcore_default_seo_panel: true
            objects:
                enabled: true
                data_classes:
                    - News
                    - Product
            enabled_integrator:
                -   integrator_name: title_description
                -   integrator_name: open_graph
                    integrator_config:
                        facebook_image_thumbnail: 'socialThumb'
                -   integrator_name: twitter_card
                    integrator_config:
                        twitter_image_thumbnail: 'socialThumb'
                -   integrator_name: schema
                -   integrator_name: html_tag
    index_provider_configuration:
        enabled_worker:
            -   worker_name: google_index
                worker_config:
                    # default pimcore: config/pimcore/google-api-private-key.json
                    auth_config: config/pimcore/google-api-private-key.json
        pimcore_element_watcher:
            enabled: true
```

6. Update app layout view in `templates` folder:
```bash
<head>
    {% apply spaceless %}
    ....................................
    {% endapply %}

    {% block metadata %}
        {{ pimcore_head_title() }}
        {{ pimcore_head_meta() }}
        {{ pimcore_head_link() }}
    {% endblock metadata %}

</head>
```

7. Update detail views in `templates` folder:
```bash
{% extends 'layout/app.html.twig' %}

{% block metadata %}
    {% do seo_update_metadata(item, app.request.locale) %}
    {{ parent() }}
{% endblock metadata %}

{% block content %}
....................................
{% endblock %}
```
