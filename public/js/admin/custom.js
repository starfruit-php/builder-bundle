$(document).ready(function() {
    // push "vi" always first of array
    var locale = 'vi';
    var locales = pimcore.settings.websiteLanguages

    const viIndex = locales.indexOf(locale);
    locales.splice(viIndex, 1);
    locales.unshift(locale);

    pimcore.settings.websiteLanguages = locales;
});