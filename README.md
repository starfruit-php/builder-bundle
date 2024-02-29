Starfruit Builder Bundle
<!-- [TOC] -->

# Installation

1. On your Pimcore 11 root project:
```bash
$ composer require starfruit/builder-bundle
```

2. Update `config/bundles.php` file:
```bash
return [
    ....
    Starfruit\BuilderBundle\StarfruitBuilderBundle::class => ['all' => true],
];
```

# Helpful function list
- Preview Asset (image) in Admin UI
- [Tool](src/Tool)
- Default config in [builder.yaml](config/pimcore/builder.yaml)
- Default layout for [templates](templates/layout)