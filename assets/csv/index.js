MyFolder.modifier.csv = function (element, info) {
    var extension = ['csv', 'tsv'];
    if (extension.includes(info.type.toLowerCase())) {
        // Array.prototype.includes() not support for old browser.
        let href = MyFolder.settings.pathInfo+info.name;
        let object = new URL(window.location.origin+MyFolder.settings.basePath+href);
        object.searchParams.append('html','');
        let newhref = object.href
        // Hilangkan origin, contoh: http://localhost
        newhref = newhref.substring(window.location.origin.length)
        // Hilangkan tanda = diakhir.
        newhref = newhref.substring(0,(newhref.length - 1))
        // Fill.
        $(element).attr('href', newhref);
    }
}
