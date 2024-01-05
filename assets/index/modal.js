/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.modal = {
    execute: function (options) {
        MyFolder.modal.process(options, ['register', 'show']);
    }
}

/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.modalRegister = {
    execute: function (options) {
        MyFolder.modal.process(options, ['register']);
    }
}

/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.modalHide = {
    execute: function (options) {
        MyFolder.modal.process(options, ['hide']);
    }
}

/**
 * Static variable property.
 */
MyFolder.modal.registry = {
    byName: {},
    // Isi dari array byQueue adalah object info dari modal,
    // atau string name yang merujuk ke infomasi yang ada di byName.
    byQueue: []
};

/**
 * Static function.
 */
MyFolder.modal.process = function (info, tasks) {
    // Get tasks.
    let init = false;
    const registry = MyFolder.modal.registry
    if (registry.byQueue.length == 0) {
        init = true;
    }

    if (tasks.includes('register')) {
        MyFolder.modal.register(info);
        if (!(tasks.includes('show'))) {
            // MyFolder.modal.register() mengisi queue, sehingga
            // perlu di reset.
            MyFolder.modal.load().reset();
        }
    }
    if (tasks.includes('show')) {
        if (init) {
            MyFolder.modal.load().toggle();
        }
        else {
            // Sembunyikan agar trigger event hide untuk menjalankan
            // method ::next();
            MyFolder.modal.load().currentModal.hide();
        }
    }
    if (tasks.includes('hide')) {
        MyFolder.modal.load().currentModal.hide();
    }
}

/**
 * Static function `MyFolder.modal.register()`.
 *
 * @param info
 *
 * Menambahkan informasi modal kedalam registry.
 */
MyFolder.modal.register = function (info) {
    const registry = MyFolder.modal.registry
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
 * Static function `MyFolder.modal.load()`.
 *
 * Menciptakan static property `MyFolder.modal.instance`, kemudian
 * mengisinya dengan object dari class `MyFolder.modal`.
 */
MyFolder.modal.load = function () {
    if (!("instance" in MyFolder.modal)) {
        MyFolder.modal.instance = new MyFolder.modal();
    }
    return MyFolder.modal.instance;
}
