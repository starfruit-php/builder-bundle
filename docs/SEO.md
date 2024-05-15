# Setup
1. Config `link_generate_objects`, see [example config](../config/pimcore/starfruit_builder.yaml)

2. Run command to create tables:
```bash
    ./bin/console builder:setup
```
3. Example code while using class `Starfruit\BuilderBundle\Model\Seo`:
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

    // get full SEO Scoring
    $scoring = $seo->getScoring();

    ...
```
