# SEO

## Setup

1. Config `link_generate_objects`, see [example config](../config/pimcore/starfruit_builder.yaml)

2. Create tables:

    ```bash
    # create tables
        ./bin/console builder:setup
    # update with option `--update` or `-u`
        ./bin/console builder:setup -u
    ```

## CMS functions

### Example code

Using class `Starfruit\BuilderBundle\Model\Seo` to get scoring result

```bash
    use Pimcore\Model\DataObject;
    use Starfruit\BuilderBundle\Model\Seo;
    ...
    // get by element and locale
    $element = DataObject::getById(1503);
    
    // use current locale
    $seo = Seo::getOrCreate($element);
    
    // OR pass custom locale
    $locale = 'vi';
    $seo = Seo::getOrCreate($element, $locale);
    
    // store keyword
    $keyword = 'car';
    $seo->setKeyword($keyword);
    $seo->save();

    // set slug
    $seo->setSlug('/vi/new-slug');
    
    // get full SEO Scoring
    $scoring = $seo->getScoring();
```

### SEO Scoring

SEO Scoring test group

| field | meaning |
| ------------ | ------------ |
| `data` | SEO data of element |
| `basic` | Basic test result |
| `additional` | Additional test result |
| `readability` | Readability test result |
| `link` | Test result for all href in element content |

Basic field in SEO Scroing result:

| field | data type | meaning |
| ------------ | ------------ | ------------ |
| `times` | int | appear times, count |
| `status` | enum(success,warning,danger) | status of a criteria in scoring test |
| `error` | boolean | need to fix |
| `statusCode` | int | return code while access a link |
| `errorTotal` | int | total of errors in scoring test group |

## Render SEO data in templates

### Setup layouts

See [template docs](TEMPLATE.md)

### Auto render

Adding setup layouts above and SEO DATA will be rendered automatically , or custom setup with functions, see [extension docs](EXTENSION.md)
