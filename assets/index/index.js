/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.index = {
    execute: function (options) {
        // Reset javascript history here.
        let state = {pathInfo: MyFolder.settings.pathInfo, pathInfoEncoded: MyFolder.settings.pathInfoEncoded}
        window.history.replaceState(state, "", MyFolder.settings.basePath + MyFolder.settings.pathInfoEncoded);
        MyFolder.index.instance = new MyFolder.index(options);
    }
}

/**
 * Implements hook `MyFolder.elementModifier()`.
 */
MyFolder.modifier.index = function (element, info) {
    let href = MyFolder.settings.basePath + MyFolder.settings.pathInfoEncoded + encodeURIComponent(info.name);
    if (info.type == '.') {
        href += '/';
    }
    $(element).attr('href', href);

}

/**
 * Implements hook `MyFolder.elementModifier()`.
 */
MyFolder.modifier.nginx = function (element, info) {
    // nginx conf: location ~ \.php dan location ~ \.htaccess
    // menyebabkan path tersebut diambil alih oleh web server,
    // sehingga perlu kita akali.
    var extensionReadByNginx = ['php', 'htaccess'];
    if (extensionReadByNginx.includes(info.type.toLowerCase())) {
        // Array.prototype.includes() not support for old browser.
        $(element).attr('href', MyFolder.settings.basePath+'/___pseudo/raw?path='+MyFolder.settings.pathInfo+info.name);
    }
}

/**
 * Eksekusi jika document sudah ready.
 */
$(document).ready(function () {
    window.onpopstate = (event) => {
        var pathInfo = event.state.pathInfo;
        var pathInfoEncoded = event.state.pathInfoEncoded;
        MyFolder.settings.pathInfo = pathInfo
        MyFolder.settings.pathInfoEncoded = pathInfoEncoded
        MyFolder.index.instance = new MyFolder.index;
    };
});
