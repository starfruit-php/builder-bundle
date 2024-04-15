pimcore.registerNS("pimcore.plugin.HelperBundle");

pimcore.plugin.HelperBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.HelperBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("HelperBundle ready!");
    }
});

var HelperBundlePlugin = new pimcore.plugin.HelperBundle();
