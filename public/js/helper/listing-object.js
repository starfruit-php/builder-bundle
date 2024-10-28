var BuilderListingObject = function(element, callbacks = {}) {
    var options = {
        url: null,  // call data
        dataBlock: null, // insert data after call
        useAppend: false, // insert by .append() or .html()
        callOneTime: true, // call data one time or always re-call
        renderAfterInit: false, // render data of first element after init or not
    }

    var callbackFunction = {
        startRender: callbacks?.startRender || function() {},
        afterRender: callbacks?.afterRender || function() {},
        afterRenderTimeout: callbacks?.afterRenderTimeout || 100,
        finishRender: callbacks?.finishRender || function() {},
    };

    init();
    $(element).bind('click', function() {
        render();
    });

    function init()
    {
        if ($(element).attr('builder-listing-object-options')) {
            var customOptions = $.parseJSON($(element).attr('builder-listing-object-options'));
            options.url = customOptions?.url;
            options.dataBlock = customOptions?.dataBlock;

            options.useAppend = customOptions?.useAppend == 'true';
            options.callOneTime = customOptions?.callOneTime !== 'false';
            options.renderAfterInit = customOptions?.renderAfterInit == 'true';
        }

        if (options.renderAfterInit) {
            render();
        }
    }

    function render()
    {
        callbackFunction.startRender(options);

        if (options.url && options.dataBlock) {
            if ((options.callOneTime && $(options.dataBlock).children().length == 0) ||
                !options.callOneTime
            ) {
                $.get(options.url, function (data, textStatus, jqXHR) {
                    options.useAppend ? $(options.dataBlock).append(data) : $(options.dataBlock).html(data);

                    setTimeout(function() {
                        callbackFunction.afterRender(options, data)
                    }, callbackFunction.afterRenderTimeout);
                }, 'html');
            }
        }

        callbackFunction.finishRender(options);
    }
}
