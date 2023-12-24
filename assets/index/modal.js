/**
 * Implements of MyFolder.attachBehaviors().
 */
MyFolder.behaviors.modal = {
    attach: function (context, settings) {
        // console.log('|-MyFolder.behaviors.modal.attach(context, settings)');
        let init = false;
        let next = false;
        const registry = MyFolder.modal.registry
        if (registry.byQueue.length == 0) {
            init = true;
        }
        if (typeof settings == 'object') {
            if ('commands' in settings) {
                settings.commands.forEach(function (value, key, array) {
                    switch (value.command) {
                        case 'modal':
                            if (!('_processed' in value)) {
                                value._processed = false;
                            }
                            if (!value._processed) {
                                value._processed = true;
                                MyFolder.modal.register(value.options);
                                next = true;
                            }
                            break;
                    }
                })
            }
        }
        if (init && registry.byQueue.length > 0) {
            // console.log('|- > MyFolder.modal.load().toggle();');
            MyFolder.modal.load().toggle();
        }
        else if (next) {
            // console.log('|- > MyFolder.modal.load().next();');
            const self = MyFolder.modal.load();
            // Sembunyikan agar trigger event hide untuk menjalankan
            // method ::next();
            self.currentModal.hide();
        }
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
 * Static function `MyFolder\modal::register()`.
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
 * Static function `MyFolder\modal::load()`.
 *
 * @param info
 *
 * Menciptakan static property `MyFolder\modal::$instance`, kemudian
 * mengisinya dengan object dari class `MyFolder\modal`.
 */
MyFolder.modal.load = function () {
    if (!("instance" in MyFolder.modal)) {
        MyFolder.modal.instance = new MyFolder.modal();
    }
    return MyFolder.modal.instance;
}
