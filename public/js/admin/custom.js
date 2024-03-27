$(document).ready(function() {
    // push "vi" always first of array
    var locale = 'vi';
    var locales = pimcore.settings.websiteLanguages;

    // if exist "vi" in list
    const viIndex = locales.indexOf(locale);
    if (viIndex > -1) {
        locales.splice(viIndex, 1);
        locales.unshift(locale);

        pimcore.settings.websiteLanguages = locales;
    }
});
