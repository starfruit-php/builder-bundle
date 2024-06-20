# SITEMAP

## Requirement

1. Sitemap setting get Class list from `link_generate_objects`, see [example config](../config/pimcore/starfruit_builder.yaml)

2. Run command to set require options:

```bash
# main-domain: website domain, ex. https://example.com
./bin/console builder:option-setting --main-domain=
```

## Setup

Get/set Class list for Sitemap setting:

```bash
use Starfruit\BuilderBundle\Sitemap\Setting;
...
// data construction:
// [
//     'key'   => using to pass in set function
//     'name'  => display name
//     'check' => status - boolean
// ]

// keys (class) for sitemap config
// get
$sitemap = Setting::getKeys();
// set
$keys = ['news', 'product'];
Setting::setKeys($keys);

// order options for sitemap config
// get
$orders = Setting::getOrder();
// set
$order = 'asc';
Setting::setOrder($order);
```

## Generate

1. Run command to generate sitemap xml files with above configs:

```bash
./bin/console builder:sitemap:generate
```

2. Useful functions:

```bash
use Starfruit\BuilderBundle\Sitemap\Generator;
...
// generate sitemap files with all sections (keys);
Generator::populate();

// generate sitemap file with a section (keys);
Generator::populate($section);

// generate main sitemap file (default);
Generator::populateIndex();
```

## Re-Generate

After updating or deteting object or document/page, automatically regenerate sitemap file with config in `starfruit_builder`, see [example config](../config/pimcore/starfruit_builder.yaml)

```bash
starfruit_builder:
    link_generate_objects:
        news:
            class_name: News
            field_create_slug: title
            field_for_slug: slug
            update_while_empty: true
            seo_fields:
                title: title
                description: shortText
                image: image
                content: contents
            sitemap:
                auto_regenerate: true # default false
    ...

    sitemap:
        document:
            auto_regenerate: true # default false
```