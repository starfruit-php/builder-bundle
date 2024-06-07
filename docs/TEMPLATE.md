# TEMPLATE

## Default template layout

Provide default master layout for .html.twig file in main project, [app.html.twig](../templates/layout/app.html.twig)

## Insert code to view

Check example and Builder will render automatically added code in view:

```bash
    use Starfruit\BuilderBundle\Model\Option;
    ....

    // set code to insert to <head> tag
    Option::setCodeHead("<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-XXXXXXX');</script>");

    // set code to insert to <body> tag
    Option::setCodeBody('<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>');

```

### Setup with Pimcore Admin

1. Add a new Snippet and set `Template` in `Settings` with `@StarfruitBuilder\config/script.html.twig`

2. Reload and update contents, then `Save & Publish` and an event-listener will store automatically.
