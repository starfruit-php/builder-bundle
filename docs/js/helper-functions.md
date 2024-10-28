# Listing objects using nav-tab layout

## Add function

```bash
{% do pimcore_head_script().appendFile(builderJsListingObject, 'text/javascript') %}
```

## Update HTML with attribute `builder-listing-object-options`

Example

```bash
{% set texts = ['an', 'un', 'en'] %}

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    {% for text in texts %}
        <li class="nav-item" role="presentation" builder-listing-object-options='{
           "url": "/test/home?item={{ text }}",
           "dataBlock": "#pills-{{ text }}",
           "useAppend": "false",
           "callOneTime": "true",
           "renderAfterInit": "{{ loop.first ? 'true': 'false' }}"
         }'>
            <a class="nav-link {{ loop.first ? 'active' : '' }}" id="{{ text }}-tab" data-toggle="pill" href="#pills-{{ text }}" role="tab" aria-controls="pills-{{ text }}" aria-selected="{{ loop.first ? 'true' : 'false' }}">{{ text }}</a>
        </li>
    {% endfor %}
</ul>
<div class="tab-content" id="pills-tabContent">
    {% for text in texts %}
        <div class="tab-pane fade {{ loop.first ? 'show active' : '' }}" id="pills-{{ text }}" role="tabpanel" aria-labelledby="{{ text }}-tab"></div>
    {% endfor %}
</div>
```

## Update custom js

```bash
$('.nav .nav-item').each(function() {
    new BuilderListingObject(this);
});

// or using callback fucntions

new BuilderListingObject($('#object-list'), {
    startRender: function(options) {
        console.log('start render');
        console.log(options);
    },
    afterRender: function(options, data) {
        console.log(data);
    },
    afterRenderTimeout: 1000,
    finishRender: function(options) {
        console.log('finish render');
        console.log(options);
    },
});
```
