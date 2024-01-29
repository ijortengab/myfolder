MyFolder.modifier.markdown = function (element, info) {
    // nginx conf: location ~ \.php dan location ~ \.htaccess
    // menyebabkan path tersebut diambil alih oleh web server,
    // sehingga perlu kita akali.
    var extensionReadByNginx = ['md', 'markdown'];
    if (extensionReadByNginx.includes(info.type.toLowerCase())) {
        // Array.prototype.includes() not support for old browser.
        let href = MyFolder.settings.pathInfo+info.name;
        let object = new URL(window.location.origin+href);
        object.searchParams.append('html','');
        let newhref = object.href
        // Hilangkan tanda = diakhir.
        let url = newhref.substring(0,(newhref.length - 1))
        $(element).attr('href', url);
    }
}
