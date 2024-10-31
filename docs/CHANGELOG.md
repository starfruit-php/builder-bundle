# CHANGELOG

## 2024-10
 - Add a new global variable `builderEditables` to get all editable data of a document in template, also use old extension `builder_render_editables(customLayouts)` [all extesions](../EXTENSION.md)

```bash
{% extends editmode ? builderEditmodeLayout : 'layouts/layouts.html.twig' %}

{% block content %}
    ...
    {# old way: use extension #}
    {% set editableData = builder_render_editables(customLayouts) %}
    {{ editableData.name }}

    {# new way: use varibale #}
    {{ builderEditables.name }}
{% endblock %}
 ```
