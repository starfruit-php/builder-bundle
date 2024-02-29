pimcore.registerNS("pimcore.plugin.StarfruitBuilderBundle");

pimcore.plugin.StarfruitBuilderBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("StarfruitBuilderBundle ready!");
    }
});

var StarfruitBuilderBundlePlugin = new pimcore.plugin.StarfruitBuilderBundle();
