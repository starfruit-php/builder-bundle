Starfruit Builder Bundle
<!-- [TOC] -->

# Installation

1. On your Pimcore 11 root project:
```bash
composer require starfruit/builder-bundle
```

2. Update `config/bundles.php` file:
```bash
return [
    ....
    Starfruit\BuilderBundle\StarfruitBuilderBundle::class => ['all' => true],
];
```

# Documents
[docs](docs)
