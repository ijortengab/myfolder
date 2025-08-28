/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.offcanvas = {
    execute: function (options) {
        MyFolder.offcanvas.process(options, ['register', 'show']);
    }
}

/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.offcanvasRegister = {
    execute: function (options) {
        MyFolder.offcanvas.process(options, ['register']);
    }
}

/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.offcanvasHide = {
    execute: function (options) {
        MyFolder.offcanvas.process(options, ['hide']);
    }
}

/**
 * Static variable property.
 */
MyFolder.offcanvas.registry = {
    byName: {},
    // Isi dari array byQueue adalah object info dari offcanvas,
    // atau string name yang merujuk ke infomasi yang ada di byName.
    byQueue: []
};

/**
 * Static function.
 */
MyFolder.offcanvas.process = function (info, tasks) {
    // Get tasks.
    let init = false;
    const registry = MyFolder.offcanvas.registry
    if (registry.byQueue.length == 0) {
        init = true;
    }

    if (tasks.includes('register')) {
        MyFolder.offcanvas.register(info);
        if (!(tasks.includes('show'))) {
            // MyFolder.offcanvas.register() mengisi queue, sehingga
            // perlu di reset.
            MyFolder.offcanvas.load().reset();
        }
    }
    if (tasks.includes('show')) {
        if (init) {
            MyFolder.offcanvas.load().toggle();
        }
        else {
            // Sembunyikan agar trigger event hide untuk menjalankan
            // method ::next();
            MyFolder.offcanvas.load().currentOffcanvas.hide();
        }
    }
    if (tasks.includes('hide')) {
        MyFolder.offcanvas.load().currentOffcanvas.hide();
    }
}

/**
 * Static function `MyFolder.offcanvas.register()`.
 *
 * @param info
 *
 * Menambahkan informasi offcanvas kedalam registry.
 */
MyFolder.offcanvas.register = function (info) {
    const registry = MyFolder.offcanvas.registry
    if (typeof info === 'object' && "name" in info) {
        let name = info.name;
        if (!(name in registry.byName)) {
            registry.byName[name] = info;
        }
        // Cegah duplikat dari sebelumnya.
        if (registry.byQueue.length > 0) {
            let last = registry.byQueue.slice(-1).pop();
            if (last != name) {
                registry.byQueue.push(name);
            }
        }
        else {
            registry.byQueue.push(name);
        }
    }
    else {
        registry.byQueue.push(info);
    }
}

/**
 * Static function `MyFolder.offcanvas.load()`.
 *
 * Menciptakan static property `MyFolder.offcanvas.instance`, kemudian
 * mengisinya dengan object dari class `MyFolder.offcanvas`.
 */
MyFolder.offcanvas.load = function () {
    if (!("instance" in MyFolder.offcanvas)) {
        MyFolder.offcanvas.instance = new MyFolder.offcanvas();
    }
    return MyFolder.offcanvas.instance;
}
