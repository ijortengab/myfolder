/**
 * Implements of MyFolder.attachBehaviors().
 */
MyFolder.behaviors.offcanvas = {
    attach: function (context, settings) {
        console.log('|-MyFolder.behaviors.offcanvas.attach(context, settings)');
        let init = false;
        let next = false;
        const registry = MyFolder.offcanvas.registry
        if (registry.byQueue.length == 0) {
            init = true;
        }
        if (typeof settings == 'object') {
            if ('commands' in settings) {
                settings.commands.forEach(function (value, key, array) {
                    switch (value.command) {
                        case 'offcanvas':
                            if (!('_processed' in value)) {
                                value._processed = false;
                            }
                            if (!value._processed) {
                                value._processed = true;
                                MyFolder.offcanvas.register(value.options);
                                next = true;
                            }
                            break;
                    }
                })
            }
        }
        if (init && registry.byQueue.length > 0) {
            // console.log('|- > MyFolder.offcanvas.load().toggle();');
            MyFolder.offcanvas.load().toggle();
        }
        else if (next) {
            // console.log('|- > MyFolder.offcanvas.load().next();');
            const self = MyFolder.offcanvas.load();
            // Sembunyikan agar trigger event hide untuk menjalankan
            // method ::next();
            self.currentOffcanvas.hide();
        }
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
 * Static function `MyFolder\offcanvas::register()`.
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
 * Static function `MyFolder\offcanvas::load()`.
 *
 * @param info
 *
 * Menciptakan static property `MyFolder\offcanvas::$instance`, kemudian
 * mengisinya dengan object dari class `MyFolder\offcanvas`.
 */
MyFolder.offcanvas.load = function () {
    if (!("instance" in MyFolder.offcanvas)) {
        MyFolder.offcanvas.instance = new MyFolder.offcanvas();
    }
    return MyFolder.offcanvas.instance;
}
