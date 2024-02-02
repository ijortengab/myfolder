/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.index = {
    execute: function (options) {
        // Reset javascript history here.
        window.history.replaceState({pathInfo: MyFolder.settings.pathInfo}, "", MyFolder.settings.basePath+MyFolder.settings.pathInfo);
        new MyFolder.index(options);
    }
}

/**
 * Eksekusi jika document sudah ready.
 */
$(document).ready(function () {
    window.onpopstate = (event) => {
        var pathInfo = event.state.pathInfo;
        MyFolder.settings.pathInfo = pathInfo
        new MyFolder.index;
    };
});
