# SITEMAP

## Requirement

1. Sitemap setting get Class list from `link_generate_objects`, see [example config](../config/pimcore/starfruit_builder.yaml)

2. Do not overwrite `pimcore_seo` config, check [here](../config/pimcore/pimcore_seo.yaml)

3. Run command to set require options:

```bash
# main-domain: website domain, ex. https://example.com
./bin/console builder:option-setting --main-domain=
```

## Setup

Get/set Class list for Sitemap setting:

```bash
use Starfruit\BuilderBundle\Sitemaps\Setting;
...
// get - data array construction:option-setting
// [
//     'key'   => using to pass in set function
//     'name'  => display name
//     'check' => status - boolean
// ]
$sitemap = Setting::getSitemap();

// set
$keys = ['news', 'product'];
Setting::setSitemap($keys);
```

## Generate

Run command to generate sitemap xml files with above configs:

```bash
./bin/console builder:sitemap:generate
```
